#------------------------------------------------------------------------------
# File:         TagInfoXML.pm
#
# Description:  Read/write tag information XML database
#
# Revisions:    2009/01/28 - P. Harvey Created
#------------------------------------------------------------------------------

package Image::ExifTool::TagInfoXML;

use strict;
require Exporter;

use vars qw($VERSION @ISA);
use Image::ExifTool qw(:Utils :Vars);
use Image::ExifTool::XMP;

$VERSION = '1.19';
@ISA = qw(Exporter);

# set this to a language code to generate Lang module with 'MISSING' entries
my $makeMissing = '';

# set this to true to override existing different descriptions/values
my $overrideDifferent;

sub LoadLangModules($);
sub WriteLangModule($$;$);
sub NumbersFirst;

# names for acknowledgements in the POD documentation
my %credits = (
    cs   => 'Jens Duttke and Petr MichE<aacute>lek',
    de   => 'Jens Duttke and Herbert Kauer',
    es   => 'Jens Duttke and Santiago del BrE<iacute>o GonzE<aacute>lez',
    fi   => 'Jens Duttke and Jarkko ME<auml>kineva',
    fr   => 'Jens Duttke, Bernard Guillotin, Jean Glasser, Jean Piquemal and Harry Nizard',
    it   => 'Jens Duttke, Ferdinando Agovino, Emilio Dati and Michele Locati',
    ja   => 'Jens Duttke and Kazunari Nishina',
    ko   => 'Jens Duttke and Jeong Beom Kim',
    nl   => 'Jens Duttke, Peter Moonen and Herman Beld',
    pl   => 'Jens Duttke and Przemyslaw Sulek',
    ru   => 'Jens Duttke, Sergey Shemetov, Dmitry Yerokhin and Anton Sukhinov',
    sv   => 'Jens Duttke and BjE<ouml>rn SE<ouml>derstrE<ouml>m',
   'tr'  => 'Jens Duttke, Hasan Yildirim and Cihan Ulusoy',
    zh_cn => 'Jens Duttke and Haibing Zhong',
    zh_tw => 'Jens Duttke and MikeF',
);

# translate country codes to language codes
my %translateLang = (
    ch_s  => 'zh_cn',
    ch_cn => 'zh_cn',
    ch_tw => 'zh_tw',
    cz    => 'cs',
    jp    => 'ja',
    kr    => 'ko',
    se    => 'sv',
);

my $caseInsensitive;    # used internally by sort routine

#------------------------------------------------------------------------------
# Utility to print tag information database as an XML list
# Inputs: 0) output file name (undef to send to console),
#         1) group name (may be undef), 2) options hash ('Flags','NoDesc')
# Returns: true on success
sub Write(;$$%)
{
    local ($_, *PTIFILE);
    my ($file, $group, %opts) = @_;
    my @groups = split ':', $group if $group;
    my $exifTool = new Image::ExifTool;
    my ($fp, $tableName, %langInfo, @langs, $defaultLang);

    Image::ExifTool::LoadAllTables();   # first load all our tables
    unless ($opts{NoDesc}) {
        LoadLangModules(\%langInfo);    # load all existing Lang modules
        @langs = sort keys %langInfo;
        $defaultLang = $Image::ExifTool::defaultLang;
    }
    if (defined $file) {
        open PTIFILE, ">$file" or return 0;
        $fp = \*PTIFILE;
    } else {
        $fp = \*STDOUT;
    }
    print $fp "<?xml version='1.0' encoding='UTF-8'?>\n";
    print $fp "<taginfo>\n\n";

    # loop through all tables and save tag names to %allTags hash
    foreach $tableName (sort keys %allTables) {
        my $table = GetTagTable($tableName);
        my $grps = $$table{GROUPS};
        my ($tagID, $didTag);
        # sort in same order as tag name documentation
        $caseInsensitive = ($tableName =~ /::XMP::/);
        # get list of languages defining elements in this table
        my $isBinary = ($$table{PROCESS_PROC} and
                        $$table{PROCESS_PROC} eq \&Image::ExifTool::ProcessBinaryData);
        # generate flattened tag names for structure fields if this is an XMP table
        if ($$table{GROUPS} and $$table{GROUPS}{0} eq 'XMP') {
            Image::ExifTool::XMP::AddFlattenedTags($table);
        }
        my @keys = sort NumbersFirst TagTableKeys($table);
        # loop throug all tag ID's in this table
        foreach $tagID (@keys) {
            my @infoArray = GetTagInfoList($table, $tagID);
            my $xmlID = Image::ExifTool::XMP::FullEscapeXML($tagID);
            # get a list of languages defining elements for this ID
            my ($index, $fam);
PTILoop:    for ($index=0; $index<@infoArray; ++$index) {
                my $tagInfo = $infoArray[$index];
                # don't list subdirectories unless they are writable
                next unless $$tagInfo{Writable} or not $$tagInfo{SubDirectory};
                if (@groups) {
                    my @tg = $exifTool->GetGroup($tagInfo);
                    foreach $group (@groups) {
                        next PTILoop unless grep /^$group$/i, @tg;
                    }
                }
                unless ($didTag) {
                    my $tname = $$table{SHORT_NAME};
                    print $fp "<table name='$tname' g0='$$grps{0}' g1='$$grps{1}' g2='$$grps{2}'>\n";
                    unless ($opts{NoDesc}) {
                        # print table description
                        my $desc = $$table{TABLE_DESC};
                        unless ($desc) {
                            ($desc = $tname) =~ s/::Main$//;
                            $desc =~ s/::/ /g;
                        }
                        # print alternate language descriptions
                        print $fp " <desc lang='en'>$desc</desc>\n";
                        foreach (@langs) {
                            $desc = $langInfo{$_}{$tableName} or next;
                            $desc = Image::ExifTool::XMP::EscapeXML($desc);
                            print $fp " <desc lang='$_'>$desc</desc>\n";
                        }
                    }
                    $didTag = 1;
                }
                my $name = $$tagInfo{Name};
                my $ind = @infoArray > 1 ? " index='$index'" : '';
                my $format = $$tagInfo{Writable} || $$table{WRITABLE};
                my $writable = $format ? 'true' : 'false';
                # check our conversions to make sure we can really write this tag
                if ($writable eq 'true') {
                    foreach ('PrintConv','ValueConv') {
                        next unless $$tagInfo{$_};
                        next if $$tagInfo{$_ . 'Inv'};
                        next if ref($$tagInfo{$_}) =~ /^(HASH|ARRAY)$/;
                        next if $$tagInfo{WriteAlso};
                        $writable = 'false';
                        last;
                    }
                }
                $format = $$tagInfo{Format} || $$table{FORMAT} if not defined $format or $format eq '1';
                $format = 'struct' if $$tagInfo{Struct};
                if (defined $format) {
                    $format =~ s/\[.*\$.*\]//;   # remove expressions from format
                } elsif ($isBinary) {
                    $format = 'int8u';
                } else {
                    $format = '?';
                }
                my $count = '';
                if ($format =~ s/\[.*?(\d*)\]$//) {
                    $count = " count='$1'" if length $1;
                }
                my @groups = $exifTool->GetGroup($tagInfo);
                my $writeGroup = $$tagInfo{WriteGroup} || $$table{WRITE_GROUP};
                if ($writeGroup and $writeGroup ne 'Comment') {
                    $groups[1] = $writeGroup;   # use common write group for group 1
                }
                # add group names if different from table defaults
                my $grp = '';
                for ($fam=0; $fam<3; ++$fam) {
                    $grp .= " g$fam='$groups[$fam]'" if $groups[$fam] ne $$grps{$fam};
                }
                # add flags if necessary
                if ($opts{Flags}) {
                    my @flags;
                    foreach (qw(Avoid Binary List Mandatory Unknown)) {
                        push @flags, $_ if $$tagInfo{$_};
                    }
                    push @flags, $$tagInfo{List} if $$tagInfo{List} and $$tagInfo{List} =~ /^(Alt|Bag|Seq)$/;
                    push @flags, 'Flattened' if defined $$tagInfo{Flat};
                    push @flags, 'Unsafe' if $$tagInfo{Protected} and $$tagInfo{Protected} & 0x01;
                    push @flags, 'Protected' if $$tagInfo{Protected} and $$tagInfo{Protected} & 0x02;
                    push @flags, 'Permanent' if $$tagInfo{Permanent} or
                        ($groups[0] eq 'MakerNotes' and not defined $$tagInfo{Permanent});
                    $grp = " flags='" . join(',', sort @flags) . "'$grp" if @flags;
                }
                print $fp " <tag id='$xmlID' name='$name'$ind type='$format'$count writable='$writable'$grp";
                if ($opts{NoDesc}) {
                    # short output format
                    print $fp "/>\n";   # empty tag element
                    next;               # no descriptions or values
                }
                my $desc = $$tagInfo{Description};
                $desc = Image::ExifTool::MakeDescription($name) unless defined $desc;
                # add alternate language descriptions and get references
                # to alternate language PrintConv hashes
                my $altDescr = '';
                my %langConv;
                foreach (@langs) {
                    my $ld = $langInfo{$_}{$name} or next;
                    if (ref $ld) {
                        $langConv{$_} = $$ld{PrintConv};
                        $ld = $$ld{Description} or next;
                    }
                    # ignore descriptions that are the same as the default language
                    next if $ld eq $desc;
                    $ld = Image::ExifTool::XMP::EscapeXML($ld);
                    $altDescr .= "\n  <desc lang='$_'>$ld</desc>";
                }
                # print tag descriptions
                $desc = Image::ExifTool::XMP::EscapeXML($desc);
                print $fp ">\n  <desc lang='$defaultLang'>$desc</desc>$altDescr\n";
                for (my $i=0; ; ++$i) {
                    my $conv = $$tagInfo{PrintConv};
                    my $idx = '';
                    if (ref $conv eq 'ARRAY') {
                        last unless $i < @$conv;
                        $conv = $$conv[$i];
                        $idx = " index='$i'";
                    } else {
                        last if $i;
                    }
                    next unless ref $conv eq 'HASH';
                    # make a list of available alternate languages
                    my @langConv = sort keys %langConv;
                    print $fp "  <values$idx>\n";
                    my $key;
                    $caseInsensitive = 0;
                    # add bitmask values to main lookup
                    if ($$conv{BITMASK}) {
                        foreach $key (keys %{$$conv{BITMASK}}) {
                            my $mask = 0x01 << $key;
                            next if $$conv{$mask};
                            $$conv{$mask} = $$conv{BITMASK}{$key};
                        }
                    }
                    foreach $key (sort NumbersFirst keys %$conv) {
                        next if $key eq 'BITMASK' or $key eq 'OTHER' or $key eq 'Notes';
                        my $val = $$conv{$key};
                        my $xmlVal = Image::ExifTool::XMP::EscapeXML($val);
                        my $xmlKey = Image::ExifTool::XMP::FullEscapeXML($key);
                        print $fp "   <key id='$xmlKey'>";
                        print $fp "\n    <val lang='$defaultLang'>$xmlVal</val>\n";
                        # add alternate language values
                        foreach (@langConv) {
                            my $lv = $langConv{$_};
                            # handle indexed PrintConv entries
                            $lv = $$lv[$i] or next if ref $lv eq 'ARRAY';
                            $lv = $$lv{$val};
                            # ignore values that are missing or same as default
                            next unless defined $lv and $lv ne $val;
                            $lv = Image::ExifTool::XMP::EscapeXML($lv);
                            print $fp "    <val lang='$_'>$lv</val>\n";
                        }
                        print $fp "   </key>\n";
                    }
                    print $fp "  </values>\n";
                }
                print $fp " </tag>\n";
            }
        }
        print $fp "</table>\n\n" if $didTag;
    }
    my $success = 1;
    print $fp "</taginfo>\n" or $success = 0;
    close $fp or $success = 0 if defined $file;
    return $success;
}

#------------------------------------------------------------------------------
# Escape backslash and quote in string
# Inputs: string
# Returns: escaped string
sub EscapePerl
{
    my $str = shift;
    $str =~ s/\\/\\\\/g;
    $str =~ s/'/\\'/g;
    return $str;
}

#------------------------------------------------------------------------------
# Generate Lang modules from input tag info XML database
# Inputs: 0) XML filename, 1) update flag:
#       undef = default (update changed modules only)
#       0 = (update changed modules only, but preserve version numbers)
#       1 = (update all, but preserve version numbers)
#       2 = (update all from scratch, but preserve version numbers)
# Returns: Count of updated Lang modules, or -1 on error
# Notes: Must be run from the directory containing 'lib'
sub BuildLangModules($;$)
{
    local ($_, *XFILE);
    my ($file, $forceUpdate) = @_;
    my ($table, $tableName, $id, $index, $valIndex, $name, $key, $lang, $defDesc);
    my (%langInfo, %different, %changed);

    Image::ExifTool::LoadAllTables();   # first load all our tables
    LoadLangModules(\%langInfo);        # load all existing Lang modules
    %langInfo = () if $forceUpdate and $forceUpdate eq '2';

    if (defined $file) {
        open XFILE, $file or return -1;
        while (<XFILE>) {
            next unless /^\s*<(\/?)(\w+)/;
            my $tok = $2;
            if ($1) {
                # close appropriate entities
                if ($tok eq 'tag') {
                    undef $id;
                    undef $index;
                    undef $name;
                    undef $defDesc;
                } elsif ($tok eq 'values') {
                    undef $key;
                    undef $valIndex;
                } elsif ($tok eq 'table') {
                    undef $table;
                    undef $id;
                }
                next;
            }
            if ($tok eq 'table') {
                /^\s*<table name='([^']+)'[ >]/ or warn('Bad table'), next;
                $tableName = "Image::ExifTool::$1";
                # ignore userdefined tables
                next if $tableName =~ /^Image::ExifTool::UserDefined/;
                $table = Image::ExifTool::GetTagTable($tableName);
                $table or warn("Unknown tag table $tableName\n");
                next;
            }
            next unless defined $table;
            if ($tok eq 'tag') {
                /^\s*<tag id='([^']*)' name='([^']+)'( index='(\d+)')?[ >]/ or warn('Bad tag'), next;
                $id = Image::ExifTool::XMP::FullUnescapeXML($1);
                $name = $2;
                $index = $4;
                $id = hex($id) if $id =~ /^0x[\da-fA-F]+$/; # convert hex ID's
                next;
            }
            if ($tok eq 'values') {
                /^\s*<values index='([^']*)'>/ or next;
                $valIndex = $1;
            } elsif ($tok eq 'key') {
                defined $id or warn('No ID'), next;
                /^\s*<key id='([^']*)'>/ or warn('Bad key'), next;
                $key = Image::ExifTool::XMP::FullUnescapeXML($1);
                $key = hex($key) if $key =~ /^0x[\da-fA-F]+$/; # convert hex keys
            } elsif ($tok eq 'val' or $tok eq 'desc') {
                /^\s*<$tok( lang='([-\w]+?)')?>(.*)<\/$tok>/ or warn("Bad $tok"), next;
                $tok eq 'desc' and defined $key and warn('Out of order "desc"'), next;
                my $lang = $2 or next; # looking only for alternate languages
                $lang =~ tr/-A-Z/_a-z/;
                # use standard ISO 639-1 language codes
                $lang = $translateLang{$lang} if $translateLang{$lang};
                my $tval = Image::ExifTool::XMP::UnescapeXML($3);
                my $val = ucfirst $tval;
                my $cap = ($tval ne $val);
                if ($makeMissing and $lang eq 'en') {
                    $lang = $makeMissing;
                    $val = 'MISSING';
                }
                my $isDefault = ($lang eq $Image::ExifTool::defaultLang);
                unless ($langInfo{$lang} or $isDefault) {
                    print "Creating new language $lang\n";
                    $langInfo{$lang} = { };
                }
                defined $name or $name = '<unknown>';
                unless (defined $id) {
                    next if $isDefault;
                    # this is a table description
                    next if $langInfo{$lang}{$tableName} and
                            $langInfo{$lang}{$tableName} eq $val;
                    $langInfo{$lang}{$tableName} = $val;
                    $changed{$lang} = 1;
                    warn("Capitalized '$lang' val for $name: $val\n") if $cap;
                    next;
                }
                my @infoArray = GetTagInfoList($table, $id);
    
                # this will fail for UserDefined tags and tags without ID's
                @infoArray or warn("Error loading tag for $tableName ID='$id'\n"), next;
                my ($tagInfo, $langInfo);
                if (defined $index) {
                    $tagInfo = $infoArray[$index];
                    $tagInfo or warn('Invalid index'), next;
                } else {
                    @infoArray > 1 and warn('Missing index'), next;
                    $tagInfo = $infoArray[0];
                }
                my $tagName = $$tagInfo{Name};
                if ($isDefault) {
                    unless ($$tagInfo{Description}) {
                        $$tagInfo{Description} = Image::ExifTool::MakeDescription($tagName);
                    }
                    $defDesc = $$tagInfo{Description};
                    $langInfo = $tagInfo;
                } else {
                    $langInfo = $langInfo{$lang}{$tagName};
                    if (not defined $langInfo) {
                        $langInfo = $langInfo{$lang}{$tagName} = { };
                    } elsif (not ref $langInfo) {
                        $langInfo = $langInfo{$lang}{$tagName} = { Description => $langInfo };
                    }
                }
                # save new value in langInfo record
                if ($tok eq 'desc') {
                    my $oldVal = $$langInfo{Description};
                    next if defined $oldVal and $oldVal eq $val;
                    if ($makeMissing) {
                        next if defined $oldVal and $val eq 'MISSING';
                    } elsif (defined $oldVal) {
                        my $t = "$lang $tagName";
                        unless (defined $different{$t} and $different{$t} eq $val) {
                            my $a = defined $different{$t} ? 'ANOTHER ' : '';
                            warn "${a}Different '$lang' desc for $tagName: $val (was $$langInfo{Description})\n";
                            next if defined $different{$t}; # don't change back again
                            $different{$t} = $val;
                        }
                        next unless $overrideDifferent;
                    }
                    next if $isDefault;
                    if (defined $defDesc and $defDesc eq $val) {
                        delete $$langInfo{Description}; # delete if same as default language
                    } else {
                        $$langInfo{Description} = $val;
                    }
                } else {
                    defined $key or warn("No key for $$tagInfo{Name}"), next;
                    my $printConv = $$tagInfo{PrintConv};
                    if (ref $printConv eq 'ARRAY') {
                        defined $valIndex or warn('No value index'), next;
                        $printConv = $$printConv[$valIndex];
                    }
                    ref $printConv eq 'HASH' or warn('No PrintConv'), next;
                    my $convVal = $$printConv{$key};
                    unless (defined $convVal) {
                        if ($$printConv{BITMASK} and $key =~ /^\d+$/) {
                            my $i;
                            for ($i=0; $i<32; ++$i) {
                                next unless $key == (0x01 << $i);
                                $convVal = $$printConv{BITMASK}{$i};
                            }
                        }
                        warn("Missing PrintConv entry for $key") and next unless defined $convVal;
                    }
                    if ($cap and $convVal =~ /^[a-z]/) {
                        $val = lcfirst $val;    # change back to lower case
                        undef $cap;
                    }
                    my $lc = $$langInfo{PrintConv};
                    $lc or $lc = $$langInfo{PrintConv} = { };
                    $lc = $printConv if ref $lc eq 'ARRAY'; #(default lang only)
                    my $oldVal = $$lc{$convVal};
                    next if defined $oldVal and $oldVal eq $val;
                    if ($makeMissing) {
                        next if defined $oldVal and $val eq 'MISSING';
                    } elsif (defined $oldVal and (not $isDefault or not $val=~/^\d+$/)) {
                        my $t = "$lang $tagName $convVal";
                        unless (defined $different{$t} and $different{$t} eq $val) {
                            my $a = defined $different{$t} ? 'ANOTHER ' : '';
                            warn "${a}Different '$lang' val for $tagName '$convVal': $val (was $oldVal)\n";
                            next if defined $different{$t}; # don't change back again
                            $different{$t} = $val;
                        }
                        next unless $overrideDifferent;
                    }
                    next if $isDefault;
                    warn("Capitalized '$lang' val for $tagName: $tval\n") if $cap;
                    $$lc{$convVal} = $val;
                }
                $changed{$lang} = 1;
            }
        }
        close XFILE;
    }
    # rewrite all changed Lang modules
    my $rtnVal = 0;
    foreach $lang ($forceUpdate ? @Image::ExifTool::langs : sort keys %changed) {
        next if $lang eq $Image::ExifTool::defaultLang;
        ++$rtnVal;
        # write this module (only increment version number if not forced)
        WriteLangModule($lang, $langInfo{$lang}, not defined $forceUpdate) or $rtnVal = -1, last;
    }
    return $rtnVal;
}

#------------------------------------------------------------------------------
# Write Lang module
# Inputs: 0) language string, 1) langInfo lookup reference, 2) flag to increment version
# Returns: true on success
sub WriteLangModule($$;$)
{
    local ($_, *XOUT);
    my ($lang, $langTags, $newVersion) = @_;
    my $err;
    -e "lib/Image/ExifTool" or die "Must run from directory containing 'lib'\n";
    my $out = "lib/Image/ExifTool/Lang/$lang.pm";
    my $tmp = "$out.tmp";
    open XOUT, ">$tmp" or die "Error creating $tmp\n";
    my $ver = "Image::ExifTool::Lang::${lang}::VERSION";
    no strict 'refs';
    if ($$ver) {
        $ver = $$ver;
        $ver = int($ver * 100 + 1.5) / 100 if $newVersion;
    } else {
        $ver = 1.0;
    }
    $ver = sprintf('%.2f', $ver);
    use strict 'refs';
    my $langName = $Image::ExifTool::langName{$lang} || $lang;
    $langName =~ s/\s*\(.*//;
    print XOUT <<HEADER;
#------------------------------------------------------------------------------
# File:         $lang.pm
#
# Description:  ExifTool $langName language translations
#
# Notes:        This file generated automatically by Image::ExifTool::TagInfoXML
#------------------------------------------------------------------------------

package Image::ExifTool::Lang::$lang;

use strict;
use vars qw(\$VERSION);

\$VERSION = '$ver';

HEADER
    print XOUT "\%Image::ExifTool::Lang::${lang}::Translate = (\n";
    # loop through all tag and table names
    my $tag;
    foreach $tag (sort keys %$langTags) {
        my $desc = $$langTags{$tag};
        my $conv;
        if (ref $desc) {
            $conv = $$desc{PrintConv};
            $desc = $$desc{Description};
            # remove description if not necessary
            # (not strictly correct -- should test against tag description, not name)
            undef $desc if $desc and $desc eq $tag;
            # remove unnecessary value translations
            if ($conv) {
                my @keys = keys %$conv;
                foreach (@keys) {
                    delete $$conv{$_} if $_ eq $$conv{$_};
                }
                undef $conv unless %$conv;
            }
        }
        if (defined $desc) {
            $desc = EscapePerl($desc);
        } else {
            next unless $conv;
        }
        print XOUT "   '$tag' => ";
        unless ($conv) {
            print XOUT "'$desc',\n";
            next;
        }
        print XOUT "{\n";
        print XOUT "      Description => '$desc',\n" if defined $desc;
        if ($conv) {
            print XOUT "      PrintConv => {\n";
            foreach (sort keys %$conv) {
                my $str = EscapePerl($_);
                my $val = EscapePerl($$conv{$_});
                print XOUT "        '$str' => '$val',\n";
            }
            print XOUT "      },\n";
        }
        print XOUT "    },\n";
    }
    # generate acknowledgements for this language
    my $ack;
    if ($credits{$lang}) {
        $ack = "Thanks to $credits{$lang} for providing this translation.";
        $ack =~ s/(.{1,76})( +|$)/$1\n/sg;  # wrap text to 76 columns
        $ack = "~head1 ACKNOWLEDGEMENTS\n\n$ack\n";
    } else {
        $ack = '';
    }
    my $footer = <<FOOTER;
);

1;  # end


__END__

~head1 NAME

Image::ExifTool::Lang::$lang.pm - ExifTool $langName language translations

~head1 DESCRIPTION

This file is used by Image::ExifTool to generate localized tag descriptions
and values.

~head1 AUTHOR

Copyright 2003-2013, Phil Harvey (phil at owl.phy.queensu.ca)

This library is free software; you can redistribute it and/or modify it
under the same terms as Perl itself.

$ack~head1 SEE ALSO

L<Image::ExifTool(3pm)|Image::ExifTool>,
L<Image::ExifTool::TagInfoXML(3pm)|Image::ExifTool::TagInfoXML>

~cut
FOOTER
    $footer =~ s/^~/=/mg;   # un-do pod obfuscation
    print XOUT $footer or $err = 1;
    close XOUT or $err = 1;
    if ($err or not rename($tmp, $out)) {
        warn "Error writing $out\n";
        unlink $tmp;
        $err = 1;
    }
    return $err ? 0 : 1;
}

#------------------------------------------------------------------------------
# load all lang modules into hash
# Inputs: 0) Hash reference
sub LoadLangModules($)
{
    my $langHash = shift;
    my $lang;
    require Image::ExifTool;
    foreach $lang (@Image::ExifTool::langs) {
        next if $lang eq $Image::ExifTool::defaultLang;
        eval "require Image::ExifTool::Lang::$lang" or warn("Can't load Lang::$lang\n"), next;
        my $xlat = "Image::ExifTool::Lang::${lang}::Translate";
        no strict 'refs';
        %$xlat or warn("Missing Info for $lang\n"), next;
        $$langHash{$lang} = \%$xlat;
        use strict 'refs';
    }
}

#------------------------------------------------------------------------------
# sort numbers first numerically, then strings alphabetically (case insensitive)
sub NumbersFirst
{
    my $rtnVal;
    my $bNum = ($b =~ /^-?[0-9]+(\.\d*)?$/);
    if ($a =~ /^-?[0-9]+(\.\d*)?$/) {
        $rtnVal = ($bNum ? $a <=> $b : -1);
    } elsif ($bNum) {
        $rtnVal = 1;
    } else {
        my ($a2, $b2) = ($a, $b);
        # expand numbers to 3 digits (with restrictions to avoid messing up ascii-hex tags)
        $a2 =~ s/(\d+)/sprintf("%.3d",$1)/eg if $a2 =~ /^(APP)?[.0-9 ]*$/ and length($a2)<16;
        $b2 =~ s/(\d+)/sprintf("%.3d",$1)/eg if $b2 =~ /^(APP)?[.0-9 ]*$/ and length($b2)<16;
        $caseInsensitive and $rtnVal = (lc($a2) cmp lc($b2));
        $rtnVal or $rtnVal = ($a2 cmp $b2);
    }
    return $rtnVal;
}

1;  # end


__END__

=head1 NAME

Image::ExifTool::TagInfoXML - Read/write tag information XML database

=head1 DESCRIPTION

This module is used to generate an XML database from all ExifTool tag
information.  The XML database may then be edited and used to re-generate
the language modules (Image::ExifTool::Lang::*).

=head1 METHODS

=head2 Write

Print complete tag information database in XML format.

  # save list of all tags
  $success = Image::ExifTool::TagInfoXML::Write('dst.xml');

  # list all IPTC tags to console, including Flags
  Image::ExifTool::TagInfoXML::Write(undef, 'IPTC', Flags => 1);

  # write all EXIF Camera tags to file
  Image::ExifTool::TagInfoXML::Write($outfile, 'exif:camera');

=over 4

=item Inputs:

0) [optional] Output file name, or undef for console output.  Output file
will be overwritten if it already exists.

1) [optional] String of group names separated by colons to specify the group
to print.  A specific IFD may not be given as a group, since EXIF tags may
be written to any IFD.  Saves all groups if not specified.

2) [optional] Hash of options values:

    Flags   - Set to output 'flags' attribute
    NoDesc  - Set to suppress output of descriptions

=item Return Value:

True on success.

=item Sample XML Output:

=back

  <?xml version='1.0' encoding='UTF-8'?>
  <taginfo>

  <table name='XMP::dc' g0='XMP' g1='XMP-dc' g2='Other'>
   <desc lang='en'>XMP Dublin Core</desc>
   <tag id='title' name='Title' type='lang-alt' writable='true' g2='Image'>
    <desc lang='en'>Title</desc>
    <desc lang='de'>Titel</desc>
    <desc lang='fr'>Titre</desc>
   </tag>
   ...
  </table>

  </taginfo>

Flags (if selected and available) are formatted as a comma-separated list of
the following possible values:  Avoid, Binary, List, Mandatory, Permanent,
Protected, Unknown and Unsafe.  See the
L<tag name documentation|Image::ExifTool::TagNames> and
lib/Image/ExifTool/README for a description of these flags.  For XMP List
tags, the list type (Alt, Bag or Seq) is also output as a flag if
applicable.

=head2 BuildLangModules

Build all Image::ExifTool::Lang modules from an XML database file.

    Image::ExifTool::TagInfoXML::BuildLangModules('src.xml');

=over 4

=item Inputs:

0) XML file name

=item Return Value:

Number of modules updated, or negative on error.

=back

=head1 AUTHOR

Copyright 2003-2013, Phil Harvey (phil at owl.phy.queensu.ca)

This library is free software; you can redistribute it and/or modify it
under the same terms as Perl itself.

=head1 SEE ALSO

L<Image::ExifTool(3pm)|Image::ExifTool>,
L<Image::ExifTool::TagNames(3pm)|Image::ExifTool::TagNames>

=cut
