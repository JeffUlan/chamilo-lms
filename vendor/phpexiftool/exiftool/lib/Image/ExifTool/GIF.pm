#------------------------------------------------------------------------------
# File:         GIF.pm
#
# Description:  Read and write GIF meta information
#
# Revisions:    10/18/2005 - P. Harvey Separated from ExifTool.pm
#               05/23/2008 - P. Harvey Added ability to read/write XMP
#               10/28/2011 - P. Harvey Added ability to read/write ICC_Profile
#
# References:   1) http://www.w3.org/Graphics/GIF/spec-gif89a.txt
#               2) http://www.adobe.com/devnet/xmp/
#               3) http://graphcomp.com/info/specs/ani_gif.html
#               4) http://www.color.org/icc_specs2.html
#------------------------------------------------------------------------------

package Image::ExifTool::GIF;

use strict;
use vars qw($VERSION);
use Image::ExifTool qw(:DataAccess :Utils);

$VERSION = '1.10';

# road map of directory locations in GIF images
my %gifMap = (
    XMP         => 'GIF',
    ICC_Profile => 'GIF',
);

%Image::ExifTool::GIF::Main = (
    GROUPS => { 2 => 'Image' },
    VARS => { NO_ID => 1 },
    NOTES => q{
        This table lists information extracted from GIF images. See
        L<http://www.w3.org/Graphics/GIF/spec-gif89a.txt> for the official GIF89a
        specification.
    },
    GIFVersion => { },
    FrameCount => { Notes => 'number of animated images' },
    Text       => { Notes => 'text displayed in image' },
    Comment    => {
        # for documentation only -- flag as writable for the docs, but
        # it won't appear in the TagLookup because there is no WRITE_PROC
        Writable => 1,
    },
    Duration   => {
        Notes => 'duration of a single animation iteration',
        PrintConv => 'sprintf("%.2f s",$val)',
    },
    ScreenDescriptor => {
        SubDirectory => { TagTable => 'Image::ExifTool::GIF::Screen' },
    },
    # GIF89a application extensions:
    ExtensionAnimation => {
        SubDirectory => { TagTable => 'Image::ExifTool::GIF::Animate' },
    },
    ExtensionXMP => { # (for documentation only)
        SubDirectory => { TagTable => 'Image::ExifTool::XMP::Main' },
    },
    ExtensionICC => { # (for documentation only)
        SubDirectory => { TagTable => 'Image::ExifTool::ICC_Profile::Main' },
    },
);

# GIF locical screen descriptor
%Image::ExifTool::GIF::Screen = (
    PROCESS_PROC => \&Image::ExifTool::ProcessBinaryData,
    GROUPS => { 2 => 'Image' },
    NOTES => 'Information extracted from the GIF logical screen descriptor.',
    0 => {
        Name => 'ImageWidth',
        Format => 'int16u',
    },
    2 => {
        Name => 'ImageHeight',
        Format => 'int16u',
    },
    4.1 => {
        Name => 'HasColorMap',
        Mask => 0x80,
        PrintConv => { 0x00 => 'No', 0x80 => 'Yes' },
    },
    4.2 => {
        Name => 'ColorResolutionDepth',
        Mask => 0x70,
        ValueConv => '($val >> 4) + 1',
    },
    4.3 => {
        Name => 'BitsPerPixel',
        Mask => 0x07,
        ValueConv => '$val + 1',
    },
    5 => 'BackgroundColor',
);

# GIF Netscape 2.0 animation extension
%Image::ExifTool::GIF::Animate = (
    PROCESS_PROC => \&Image::ExifTool::ProcessBinaryData,
    GROUPS => { 2 => 'Image' },
    NOTES => 'Information extracted from the "NETSCAPE2.0" animation extension.',
    2 => {
        Name => 'AnimationIterations',
        Format => 'int16u',
        PrintConv => '$val ? $val : "Infinite"',
    },
);

#------------------------------------------------------------------------------
# Process meta information in GIF image
# Inputs: 0) ExifTool object reference, 1) Directory information ref
# Returns: 1 on success, 0 if this wasn't a valid GIF file, or -1 if
#          an output file was specified and a write error occurred
sub ProcessGIF($$)
{
    my ($exifTool, $dirInfo) = @_;
    my $outfile = $$dirInfo{OutFile};
    my $raf = $$dirInfo{RAF};
    my $verbose = $exifTool->Options('Verbose');
    my $out = $exifTool->Options('TextOut');
    my ($a, $s, $ch, $length, $buff);
    my ($err, $newComment, $setComment, $nvComment);
    my ($addDirs, %doneDir);
    my ($frameCount, $delayTime) = (0, 0);

    # verify this is a valid GIF file
    return 0 unless $raf->Read($buff, 6) == 6
        and $buff =~ /^GIF(8[79]a)$/
        and $raf->Read($s, 7) == 7;

    my $ver = $1;
    my $rtnVal = 0;
    my $tagTablePtr = GetTagTable('Image::ExifTool::GIF::Main');
    SetByteOrder('II');

    if ($outfile) {
        $exifTool->InitWriteDirs(\%gifMap, 'XMP'); # make XMP the preferred group for GIF
        $addDirs = $exifTool->{ADD_DIRS};
        # determine if we are editing the File:Comment tag
        my $delGroup = $exifTool->{DEL_GROUP};
        $newComment = $exifTool->GetNewValues('Comment', \$nvComment);
        $setComment = 1 if $nvComment or $$delGroup{File};
        # change to GIF 89a if adding comment, XMP or ICC_Profile
        $buff = 'GIF89a' if $$addDirs{XMP} or $$addDirs{ICC_Profile} or defined $newComment;
        Write($outfile, $buff, $s) or $err = 1;
    } else {
        $exifTool->SetFileType();   # set file type
        $exifTool->HandleTag($tagTablePtr, 'GIFVersion', $ver);
        $exifTool->HandleTag($tagTablePtr, 'ScreenDescriptor', $s);
    }
    my $flags = Get8u(\$s, 4);
    if ($flags & 0x80) { # does this image contain a color table?
        # calculate color table size
        $length = 3 * (2 << ($flags & 0x07));
        $raf->Read($buff, $length) == $length or return 0; # skip color table
        Write($outfile, $buff) or $err = 1 if $outfile;
    }
#
# loop through GIF blocks
#
Block:
    for (;;) {
        last unless $raf->Read($ch, 1);
        # write out any new metadata now if this isn't an extension block
        if ($outfile and ord($ch) != 0x21) {
            # write the comment first if necessary
            if (defined $newComment and $$nvComment{IsCreating}) {
                # write comment marker
                Write($outfile, "\x21\xfe") or $err = 1;
                $verbose and print $out "  + Comment = $newComment\n";
                my $len = length($newComment);
                # write out the comment in 255-byte chunks, each
                # chunk beginning with a length byte
                my $n;
                for ($n=0; $n<$len; $n+=255) {
                    my $size = $len - $n;
                    $size > 255 and $size = 255;
                    my $str = substr($newComment,$n,$size);
                    Write($outfile, pack('C',$size), $str) or $err = 1;
                }
                Write($outfile, "\0") or $err = 1;  # empty chunk as terminator
                undef $newComment;
                undef $nvComment;   # delete any other extraneous comments
                ++$exifTool->{CHANGED};     # increment file changed flag
            }
            # add application extension containing XMP block if necessary
            # (this will place XMP before the first non-extension block)
            if (exists $$addDirs{XMP} and not defined $doneDir{XMP}) {
                $doneDir{XMP} = 1;
                # write new XMP data
                my $xmpTable = GetTagTable('Image::ExifTool::XMP::Main');
                my %dirInfo = ( Parent => 'GIF' );
                $verbose and print $out "Creating XMP application extension block:\n";
                $buff = $exifTool->WriteDirectory(\%dirInfo, $xmpTable);
                if (defined $buff and length $buff) {
                    my $lz = pack('C*',1,reverse(0..255),0);
                    Write($outfile, "\x21\xff\x0bXMP DataXMP", $buff, $lz) or $err = 1;
                    ++$doneDir{XMP};    # set to 2 to indicate we added XMP
                } else {
                    $verbose and print $out "  -> no XMP to add\n";
                }
            }
            # add application extension containing ICC_Profile if necessary
            if (exists $$addDirs{ICC_Profile} and not defined $doneDir{ICC_Profile}) {
                $doneDir{ICC_Profile} = 1;
                # write new ICC_Profile
                my $iccTable = GetTagTable('Image::ExifTool::ICC_Profile::Main');
                my %dirInfo = ( Parent => 'GIF' );
                $verbose and print $out "Creating ICC_Profile application extension block:\n";
                $buff = $exifTool->WriteDirectory(\%dirInfo, $iccTable);
                if (defined $buff and length $buff) {
                    my $pos = 0;
                    Write($outfile, "\x21\xff\x0bICCRGBG1012") or $err = 1;
                    my $len = length $buff;
                    while ($pos < $len) {
                        my $n = $len - $pos;
                        $n = 255 if $n > 255;
                        Write($outfile, chr($n), substr($buff, $pos, $n)) or $err = 1;
                        $pos += $n;
                    }
                    Write($outfile, "\0") or $err = 1;  # write null terminator
                    ++$doneDir{ICC_Profile};    # set to 2 to indicate we added a new profile
                } else {
                    $verbose and print $out "  -> no ICC_Profile to add\n";
                }
            }
        }
        if (ord($ch) == 0x2c) {
            ++$frameCount;
            Write($outfile, $ch) or $err = 1 if $outfile;
            # image descriptor
            last unless $raf->Read($buff, 8) == 8 and $raf->Read($ch, 1);
            Write($outfile, $buff, $ch) or $err = 1 if $outfile;
            if ($verbose) {
                my ($left, $top, $w, $h) = unpack('v*', $buff);
                print $out "Image: left=$left top=$top width=$w height=$h\n";
            }
            if (ord($ch) & 0x80) { # does color table exist?
                $length = 3 * (2 << (ord($ch) & 0x07));
                # skip the color table
                last unless $raf->Read($buff, $length) == $length;
                Write($outfile, $buff) or $err = 1 if $outfile;
            }
            # skip "LZW Minimum Code Size" byte
            last unless $raf->Read($buff, 1);
            Write($outfile,$buff) or $err = 1 if $outfile;
            # skip image blocks
            for (;;) {
                last unless $raf->Read($ch, 1);
                Write($outfile, $ch) or $err = 1 if $outfile;
                last unless ord($ch);
                last unless $raf->Read($buff, ord($ch));
                Write($outfile,$buff) or $err = 1 if $outfile;
            }
            next;  # continue with next field
        }
#               last if ord($ch) == 0x3b;  # normal end of GIF marker
        unless (ord($ch) == 0x21) {
            if ($outfile) {
                Write($outfile, $ch) or $err = 1;
                # copy the rest of the file
                while ($raf->Read($buff, 65536)) {
                    Write($outfile, $buff) or $err = 1;
                }
            }
            $rtnVal = 1;
            last;
        }
        # get extension block type/size
        last unless $raf->Read($s, 2) == 2;
        # get marker and block size
        ($a,$length) = unpack("C"x2, $s);

        if ($a == 0xfe) {                           # comment extension

            my $comment = '';
            while ($length) {
                last unless $raf->Read($buff, $length) == $length;
                if ($verbose > 2 and not $outfile) {
                    Image::ExifTool::HexDump(\$buff, undef, Out => $out);
                }
                # add buffer to comment string
                $comment .= $buff;
                last unless $raf->Read($ch, 1);  # read next block header
                $length = ord($ch);  # get next block size
            }
            last if $length;    # was a read error if length isn't zero
            if ($outfile) {
                my $isOverwriting;
                if ($setComment) {
                    if ($nvComment) {
                        $isOverwriting = $exifTool->IsOverwriting($nvComment,$comment);
                        # get new comment again (may have been shifted)
                        $newComment = $exifTool->GetNewValues($nvComment) if defined $newComment;
                    } else {
                        # group delete, or deleting additional comments after writing one
                        $isOverwriting = 1;
                    }
                }
                if ($isOverwriting) {
                    ++$exifTool->{CHANGED};     # increment file changed flag
                    $exifTool->VerboseValue('- Comment', $comment);
                    $comment = $newComment;
                    $exifTool->VerboseValue('+ Comment', $comment) if defined $comment;
                    undef $nvComment;   # just delete remaining comments
                } else {
                    undef $setComment;  # leave remaining comments alone
                }
                if (defined $comment) {
                    # write comment marker
                    Write($outfile, "\x21\xfe") or $err = 1;
                    my $len = length($comment);
                    # write out the comment in 255-byte chunks, each
                    # chunk beginning with a length byte
                    my $n;
                    for ($n=0; $n<$len; $n+=255) {
                        my $size = $len - $n;
                        $size > 255 and $size = 255;
                        my $str = substr($comment,$n,$size);
                        Write($outfile, pack('C',$size), $str) or $err = 1;
                    }
                    Write($outfile, "\0") or $err = 1;  # empty chunk as terminator
                }
                undef $newComment;  # don't write the new comment again
            } else {
                $rtnVal = 1;
                $exifTool->FoundTag('Comment', $comment) if $comment;
                undef $comment;
                # assume no more than one comment in FastScan mode
                last if $exifTool->Options('FastScan');
            }
            next;

        } elsif ($a == 0xff and $length == 0x0b) {  # application extension

            last unless $raf->Read($buff, $length) == $length;
            if ($verbose) {
                my @a = unpack('a8a3', $buff);
                s/\0.*//s foreach @a;
                print $out "Application Extension: @a\n";
            }
            if ($buff eq 'XMP DataXMP') {   # XMP data (ref 2)

                my $hdr = "$ch$s$buff";
                # read XMP data
                my $xmp = '';
                for (;;) {
                    $raf->Read($ch, 1) or last Block;   # read next block header
                    $length = ord($ch) or last;         # get next block size
                    $raf->Read($buff, $length) == $length or last Block;
                    $xmp .= $ch . $buff;
                }
                # get length of XMP without landing zone data
                # (note that LZ data may not be exactly the same as what we use)
                my $xmpLen;
                if ($xmp =~ /<\?xpacket end=['"][wr]['"]\?>/g) {
                    $xmpLen = pos($xmp);
                } else {
                    $xmpLen = length($xmp);
                }
                my %dirInfo = (
                    DataPt  => \$xmp,
                    DataLen => length $xmp,
                    DirLen  => $xmpLen,
                    Parent  => 'GIF',
                );
                my $xmpTable = GetTagTable('Image::ExifTool::XMP::Main');
                if ($outfile) {
                    if ($doneDir{XMP} and $doneDir{XMP} > 1) {
                        $exifTool->Warn('Duplicate XMP block created');
                    }
                    $buff = $exifTool->WriteDirectory(\%dirInfo, $xmpTable);
                    if (not defined $buff) {
                        # rewrite original XMP with landing zone (adding back null terminator)
                        Write($outfile, $hdr, $xmp, "\0") or $err = 1;
                        $doneDir{XMP} = 1;
                    } elsif (length $buff) {
                        if ($buff =~ /\0/) { # (check just to be safe)
                            $exifTool->Error('XMP contained NULL character');
                        } else {
                            # write new XMP and landing zone
                            my $lz = pack('C*',1,reverse(0..255),0);
                            Write($outfile, $hdr, $buff, $lz) or $err = 1;
                        }
                        $doneDir{XMP} = 1;
                    } # else we are deleting the XMP
                } else {
                    $exifTool->ProcessDirectory(\%dirInfo, $xmpTable);
                }
                next;

            } elsif ($buff eq 'ICCRGBG1012') {      # ICC_Profile extension (ref 4)

                my $hdr = "$ch$s$buff";
                # read ICC profile data
                my $icc_profile = '';
                for (;;) {
                    $raf->Read($ch, 1) or last Block;   # read next block header
                    $length = ord($ch) or last;         # get next block size
                    $raf->Read($buff, $length) == $length or last Block;
                    $icc_profile .= $buff;
                }
                my %dirInfo = (
                    DataPt  => \$icc_profile,
                    DataLen => length $icc_profile,
                    DirLen  => length $icc_profile,
                    Parent  => 'GIF',
                );
                my $iccTable = GetTagTable('Image::ExifTool::ICC_Profile::Main');
                if ($outfile) {
                    if ($doneDir{ICC_Profile} and $doneDir{ICC_Profile} > 1) {
                        $exifTool->Warn('Duplicate ICC_Profile block created');
                    }
                    $buff = $exifTool->WriteDirectory(\%dirInfo, $iccTable);
                    # rewrite original ICC_Profile if nothing changed 
                    $buff = $icc_profile unless defined $buff;
                    if (length $buff) {
                        # write ICC profile sub-blocks
                        my $pos = 0;
                        Write($outfile, $hdr) or $err = 1;
                        my $len = length $buff;
                        while ($pos < $len) {
                            my $n = $len - $pos;
                            $n = 255 if $n > 255;
                            Write($outfile, chr($n), substr($buff, $pos, $n)) or $err = 1;
                            $pos += $n;
                        }
                        Write($outfile, "\0") or $err = 1;  # write null terminator
                        $doneDir{ICC_Profile} = 1;
                    } # else we are deleting the ICC profile
                } else {
                    $exifTool->ProcessDirectory(\%dirInfo, $iccTable);
                }
                next;

            } elsif ($buff eq 'NETSCAPE2.0') {      # animated GIF extension (ref 3)

                $raf->Read($buff, 5) == 5 or last;
                # make sure this contains the expected data
                if ($buff =~ /^\x03\x01(..)\0$/s) {
                    $exifTool->HandleTag($tagTablePtr, 'ExtensionAnimation', $buff);
                }
                $raf->Seek(-$length-5, 1) or last;  # seek back to start of block

            } else {

                # rewind to start of application extension to copy the unknown block
                $raf->Seek(-$length, 1) or last;
            }

        } elsif ($a == 0xf9 and $length == 4) {     # graphic control extension

            last unless $raf->Read($buff, $length) == $length;
            # sum the indivual delay times
            my $delay = Get16u(\$buff, 1);
            $delayTime += $delay;
            $verbose and printf $out "Graphic Control: delay=%.2f\n", $delay / 100;
            $raf->Seek(-$length, 1) or last;

        } elsif ($a == 0x01 and $length == 12) {    # plain text extension

            last unless $raf->Read($buff, $length) == $length;
            Write($outfile, $ch, $s, $buff) or $err = 1 if $outfile;
            if ($verbose) {
                my ($left, $top, $w, $h) = unpack('v4', $buff);
                print $out "Text: left=$left top=$top width=$w height=$h\n";
            }
            my $text = '';
            for (;;) {
                last unless $raf->Read($ch, 1);
                $length = ord($ch) or last;
                last unless $raf->Read($buff, $length) == $length;
                Write($outfile, $ch, $buff) or $err = 1 if $outfile; # write block
                $text .= $buff;
            }
            Write($outfile, "\0") or $err = 1 if $outfile;  # write terminator block
            $exifTool->HandleTag($tagTablePtr, 'Text', $text);
            next;
        }
        Write($outfile, $ch, $s) or $err = 1 if $outfile;
        # skip the block
        while ($length) {
            last unless $raf->Read($buff, $length) == $length;
            Write($outfile, $buff) or $err = 1 if $outfile;
            last unless $raf->Read($ch, 1);  # read next block header
            Write($outfile, $ch) or $err = 1 if $outfile;
            $length = ord($ch);  # get next block size
        }
    }
    unless ($outfile) {
        $exifTool->HandleTag($tagTablePtr, 'FrameCount', $frameCount) if $frameCount > 1;
        $exifTool->HandleTag($tagTablePtr, 'Duration', $delayTime/100) if $delayTime;
    }

    # set return value to -1 if we only had a write error
    $rtnVal = -1 if $rtnVal and $err;
    return $rtnVal;
}


1;  #end

__END__

=head1 NAME

Image::ExifTool::GIF - Read and write GIF meta information

=head1 SYNOPSIS

This module is loaded automatically by Image::ExifTool when required.

=head1 DESCRIPTION

This module contains definitions required by Image::ExifTool to read and
write GIF meta information.

=head1 AUTHOR

Copyright 2003-2013, Phil Harvey (phil at owl.phy.queensu.ca)

This library is free software; you can redistribute it and/or modify it
under the same terms as Perl itself.

=head1 REFERENCES

=over 4

=item L<http://www.w3.org/Graphics/GIF/spec-gif89a.txt>

=item L<http://www.adobe.com/devnet/xmp/>

=item L<http://graphcomp.com/info/specs/ani_gif.html>

=item L<http://www.color.org/icc_specs2.html>

=back

=head1 SEE ALSO

L<Image::ExifTool(3pm)|Image::ExifTool>

=cut
