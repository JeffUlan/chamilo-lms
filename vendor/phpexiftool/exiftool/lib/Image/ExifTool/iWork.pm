#------------------------------------------------------------------------------
# File:         iWork.pm
#
# Description:  Read Apple iWork '09 XML+ZIP files
#
# Revisions:    2009/11/11 - P. Harvey Created
#------------------------------------------------------------------------------

package Image::ExifTool::iWork;

use strict;
use vars qw($VERSION);
use Image::ExifTool qw(:DataAccess :Utils);
use Image::ExifTool::XMP;
use Image::ExifTool::ZIP;

$VERSION = '1.02';

# test for recognized iWork document extensions and outter XML elements
my %iWorkType = (
    # file extensions
    NUMBERS => 'Apple Numbers',
    PAGES   => 'Apple Pages',
    KEY     => 'Apple Keynote',
    KTH     => 'Apple Keynote Theme',
    NMBTEMPLATE => 'Apple Numbers Template',
    # we don't support double extensions --
    # "PAGES.TEMPLATE" => 'Apple Pages Template',
    # outter XML elements
    'ls:document' => 'Apple Numbers',
    'sl:document' => 'Apple Pages',
    'key:presentation' => 'Apple Keynote',
);

# MIME types for iWork files (Apple has not registered these yet, but these
# are my best guess after doing some googling.  I'm not 100% sure what "sff"
# indicates, but I think it refers to the new "flattened" package format)
my %mimeType = (
    'Apple Numbers' => 'application/x-iwork-numbers-sffnumbers',
    'Apple Pages'   => 'application/x-iwork-pages-sffpages',
    'Apple Keynote' => 'application/x-iWork-keynote-sffkey',
    'Apple Numbers Template' => 'application/x-iwork-numbers-sfftemplate',
    'Apple Pages Template'   => 'application/x-iwork-pages-sfftemplate',
    'Apple Keynote Theme'    => 'application/x-iWork-keynote-sffkth',
);

# iWork tags
%Image::ExifTool::iWork::Main = (
    GROUPS => { 0 => 'XML', 1 => 'XML', 2 => 'Document' },
    PROCESS_PROC => \&Image::ExifTool::XMP::ProcessXMP,
    VARS => { NO_ID => 1 },
    NOTES => q{
        The Apple iWork '09 file format is a ZIP archive containing XML files
        similar to the Office Open XML (OOXML) format.  Metadata tags in iWork
        files are extracted even if they don't appear below.
    },
    authors   => { Name => 'Author', Groups => { 2 => 'Author' } },
    comment   => { },
    copyright => { Groups => { 2 => 'Author' } },
    keywords  => { },
    projects  => { List => 1 },
    title     => { },
);

#------------------------------------------------------------------------------
# Generate a tag ID for this XML tag
# Inputs: 0) tag property name list ref
# Returns: tagID
sub GetTagID($)
{
    my $props = shift;
    return 0 if $$props[-1] =~ /^\w+:ID$/;  # ignore ID tags
    return ($$props[0] =~ /.*?:(.*)/) ? $1 : $$props[0];
}

#------------------------------------------------------------------------------
# We found an XMP property name/value
# Inputs: 0) ExifTool object ref, 1) tag table ref
#         2) reference to array of XMP property names (last is current property)
#         3) property value, 4) attribute hash ref (not used here)
# Returns: 1 if valid tag was found
sub FoundTag($$$$;$)
{
    my ($exifTool, $tagTablePtr, $props, $val, $attrs) = @_;
    return 0 unless @$props;
    my $verbose = $exifTool->Options('Verbose');

    $exifTool->VPrint(0, "  | - Tag '", join('/',@$props), "'\n") if $verbose > 1;

    # un-escape XML character entities
    $val = Image::ExifTool::XMP::UnescapeXML($val);
    # convert from UTF8 to ExifTool Charset
    $val = $exifTool->Decode($val, 'UTF8');
    my $tag = GetTagID($props) or return 0;

    # add any unknown tags to table
    unless ($$tagTablePtr{$tag}) {
        $exifTool->VPrint(0, "  [adding $tag]\n") if $verbose;
        AddTagToTable($tagTablePtr, $tag, { Name => ucfirst $tag });
    }
    # save the tag
    $exifTool->HandleTag($tagTablePtr, $tag, $val);

    return 1;
}

#------------------------------------------------------------------------------
# Extract information from an iWork file
# Inputs: 0) ExifTool object reference, 1) dirInfo reference
# Returns: 1
# Notes: Upon entry to this routine, the file type has already been verified
# as ZIP and the dirInfo hash contains a 'ZIP' Archive::Zip object reference
sub Process_iWork($$)
{
    my ($exifTool, $dirInfo) = @_;
    my $zip = $$dirInfo{ZIP};
    my ($type, $index, $indexFile, $status);

    # try to determine the file type
    local $SIG{'__WARN__'} = \&Image::ExifTool::ZIP::WarnProc;
    # trust type given by file extension if available
    $type = $iWorkType{$$exifTool{FILE_EXT}} if $$exifTool{FILE_EXT};
    unless ($type) {
        # read the index file
        my @members = $zip->membersMatching('^index\.(xml|apxl)$');
        if (@members) {
            ($index, $status) = $zip->contents($members[0]);
            unless ($status) {
                $indexFile = $members[0]->fileName();
                if ($index =~ /^\s*<\?xml version=[^<]+<(\w+:\w+)/s) {
                    $type = $iWorkType{$1} if $iWorkType{$1};
                }
            }
        }
        $type or $type = 'ZIP';     # assume ZIP by default
    }
    $exifTool->SetFileType($type, $mimeType{$type});

    my @members = $zip->members();
    my $docNum = 0;
    my $member;
    foreach $member (@members) {
        # get filename of this ZIP member
        my $file = $member->fileName();
        next unless defined $file;
        $exifTool->VPrint(0, "File: $file\n");
        # set the document number and extract ZIP tags
        $$exifTool{DOC_NUM} = ++$docNum;
        Image::ExifTool::ZIP::HandleMember($exifTool, $member);

        # process only the index XML and JPEG thumbnail files
        next unless $file =~ m{^(index\.(xml|apxl)|QuickLook/Thumbnail\.jpg)$}i;
        # get the file contents if necessary
        # (CAREFUL! $buff MUST be local since we hand off a value ref to PreviewImage)
        my ($buff, $buffPt);
        if ($indexFile and $indexFile eq $file) {
            # use the index file we already loaded
            $buffPt = \$index;
        } else {
            ($buff, $status) = $zip->contents($member);
            $status and $exifTool->Warn("Error extracting $file"), next;
            $buffPt = \$buff;
        }
        # extract JPEG as PreviewImage (should only be QuickLook/Thumbnail.jpg)
        if ($file =~ /\.jpg$/) {
            $exifTool->FoundTag('PreviewImage', $buffPt);
            next;
        }
        # process "metadata" section of XML index file
        next unless $$buffPt =~ /<(\w+):metadata>/g;
        my $ns = $1;
        my $p1 = pos $$buffPt;
        next unless $$buffPt =~ m{</${ns}:metadata>}g;
        # construct XML data from "metadata" section only
        $$buffPt = '<?xml version="1.0"?>' . substr($$buffPt, $p1, pos($$buffPt)-$p1);
        my %dirInfo = (
            DataPt => $buffPt,
            DirLen => length $$buffPt,
            DataLen => length $$buffPt,
            XMPParseOpts => {
                FoundProc => \&FoundTag,
            },
        );
        my $tagTablePtr = GetTagTable('Image::ExifTool::iWork::Main');
        $exifTool->ProcessDirectory(\%dirInfo, $tagTablePtr);
        undef $$buffPt; # (free memory now)
    }
    delete $$exifTool{DOC_NUM};
    return 1;
}

1;  # end

__END__

=head1 NAME

Image::ExifTool::iWork - Read Apple iWork '09 XML+ZIP files

=head1 SYNOPSIS

This module is used by Image::ExifTool

=head1 DESCRIPTION

This module contains definitions required by Image::ExifTool to extract meta
information from Apple iWork '09 XML+ZIP files.

=head1 AUTHOR

Copyright 2003-2013, Phil Harvey (phil at owl.phy.queensu.ca)

This library is free software; you can redistribute it and/or modify it
under the same terms as Perl itself.

=head1 SEE ALSO

L<Image::ExifTool::TagNames/iWork Tags>,
L<Image::ExifTool::TagNames/OOXML Tags>,
L<Image::ExifTool(3pm)|Image::ExifTool>

=cut

