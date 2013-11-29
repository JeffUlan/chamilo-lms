# Before "make install", this script should be runnable with "make test".
# After "make install" it should work as "perl t/M2TS.t".

BEGIN { $| = 1; print "1..2\n"; $Image::ExifTool::noConfig = 1; }
END {print "not ok 1\n" unless $loaded;}

# test 1: Load the module(s)
use Image::ExifTool 'ImageInfo';
use Image::ExifTool::M2TS;
$loaded = 1;
print "ok 1\n";

use t::TestLib;

my $testname = 'M2TS';
my $testnum = 1;

# test 2: Extract information from test image
{
    ++$testnum;
    my $exifTool = new Image::ExifTool;
    $exifTool->Options(Unknown => 1);
    my $info = $exifTool->ImageInfo('t/images/M2TS.mts');
    print 'not ' unless check($exifTool, $info, $testname, $testnum);
    print "ok $testnum\n";
}


# end
