<?php
/**
 * Image_Text.
 *
 * This is the main file of the Image_Text package. This file has to be included for
 * usage of Image_Text.
 *
 * This is a simple example script, showing Image_Text's facilities.
 *
 * PHP version 5
 *
 * @category  Image
 * @package   Image_Text
 * @author    Tobias Schlitt <toby@php.net>
 * @copyright 1997-2005 The PHP Group
 * @license   http://www.php.net/license/3_01.txt PHP License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Image_Text
 * @since     File available since Release 0.0.1
 */
require_once 'Image/Text/Exception.php';
/**
 * Image_Text - Advanced text manipulations in images.
 *
 * Image_Text provides advanced text manipulation facilities for GD2 image generation
 * with PHP. Simply add text clippings to your images, let the class automatically
 * determine lines, rotate text boxes around their center or top left corner. These
 * are only a couple of features Image_Text provides.
 *
 * @category  Image
 * @package   Image_Text
 * @author    Tobias Schlitt <toby@php.net>
 * @copyright 1997-2005 The PHP Group
 * @license   http://www.php.net/license/3_01.txt PHP License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Image_Text
 * @since     0.0.1
 */
class Image_Text
{
    /**
     * Regex to match HTML style hex triples.
     */
    const IMAGE_TEXT_REGEX_HTMLCOLOR
        = "/^[#|]([a-f0-9]{2})?([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i";

    /**
     * Defines horizontal alignment to the left of the text box. (This is standard.)
     */
    const IMAGE_TEXT_ALIGN_LEFT = "left";

    /**
     * Defines horizontal alignment to the center of the text box.
     */
    const IMAGE_TEXT_ALIGN_RIGHT = "right";

    /**
     * Defines horizontal alignment to the center of the text box.
     */
    const IMAGE_TEXT_ALIGN_CENTER = "center";

    /**
     * Defines vertical alignment to the to the top of the text box. (This is
     * standard.)
     */
    const IMAGE_TEXT_ALIGN_TOP = "top";

    /**
     * Defines vertical alignment to the to the middle of the text box.
     */
    const IMAGE_TEXT_ALIGN_MIDDLE = "middle";

    /**
     * Defines vertical alignment to the to the bottom of the text box.
     */
    const IMAGE_TEXT_ALIGN_BOTTOM = "bottom";

    /**
     * TODO: This constant is useless until now, since justified alignment does not
     * work yet
     */
    const IMAGE_TEXT_ALIGN_JUSTIFY = "justify";

    /**
     * Options array. these options can be set through the constructor or the set()
     * method.
     *
     * Possible options to set are:
     * <pre>
     *   'x'                | This sets the top left coordinates (using x/y) or the
     *   'y'                | center point coordinates (using cx/cy) for your text
     *   'cx'               | box. The values from cx/cy will overwrite x/y.
     *   'cy'               |
     *
     *   'canvas'           | You can set different values as a canvas:
     *                      |   - A gd image resource.
     *                      |   - An array with 'width' and 'height'.
     *                      |   - Nothing (the canvas will be measured after the real
     *                      |     text size).
     *
     *   'antialias'        | This is usually true. Set it to false to switch
     *                      | antialiasing off.
     *
     *   'width'            | The width and height for your text box.
     *   'height'           |
     *
     *   'halign'           | Alignment of your text inside the textbox. Use
     *   'valign'           | alignment constants to define vertical and horizontal
     *                      | alignment.
     *
     *   'angle'            | The angle to rotate your text box.
     *
     *   'color'            | An array of color values. Colors will be rotated in the
     *                      | mode you choose (linewise or paragraphwise). Can be in
     *                      | the following formats:
     *                      |   - String representing HTML style hex couples
     *                      |     (+ unusual alpha couple in the first place,
     *                      |      optional).
     *                      |   - Array of int values using 'r', 'g', 'b' and
     *                      |     optionally 'a' as keys.
     *
     *   'color_mode'       | The color rotation mode for your color sets. Does only
     *                      | apply if you defined multiple colors. Use 'line' or
     *                      | 'paragraph'.
     *
     *   'background_color' | defines the background color. NULL sets it transparent
     *   'enable_alpha'     | if alpha channel should be enabled. Automatically
     *                      | enabled when background_color is set to NULL
     *
     *   'font_path'        | Location of the font to use. The path only gives the
     *                      | directory path (ending with a /).
     *   'font_file'        | The fontfile is given in the 'font_file' option.
     *
     *   'font_size'        | The font size to render text in (will be overwriten, if
     *                      | you use automeasurize).
     *
     *   'line_spacing'     | Measure for the line spacing to use. Default is 0.5.
     *
     *   'min_font_size'    | Automeasurize settings. Try to keep this area as small
     *   'max_font_size'    | as possible to get better performance.
     *
     *   'image_type'       | The type of image (use image type constants). Is
     *                      | default set to PNG.
     *
     *   'dest_file'        | The destination to (optionally) save your file.
     * </pre>
     *
     * @var array
     * @see Image_Text::set()
     */

    private $_options = array(
        // orientation
        'x' => 0,
        'y' => 0,

        // surface
        'canvas' => null,
        'antialias' => true,

        // text clipping
        'width' => 0,
        'height' => 0,

        // text alignment inside the clipping
        'halign' => self::IMAGE_TEXT_ALIGN_LEFT,
        'valign' => self::IMAGE_TEXT_ALIGN_TOP,

        // angle to rotate the text clipping
        'angle' => 0,

        // color settings
        'color' => array('#000000'),

        'color_mode' => 'line',

        'background_color' => '#000000',
        'enable_alpha' => false,

        // font settings
        'font_path' => "./",
        'font_file' => null,
        'font_size' => 2,
        'line_spacing' => 0.5,

        // automasurizing settings
        'min_font_size' => 1,
        'max_font_size' => 100,

        //max. lines to render
        'max_lines' => 100,

        // misc settings
        'image_type' => IMAGETYPE_PNG,
        'dest_file' => ''
    );

    /**
     * Contains option names, which can cause re-initialization force.
     *
     * @var array
     */
    private $_reInits = array(
        'width', 'height', 'canvas', 'angle', 'font_file', 'font_path', 'font_size'
    );

    /**
     * The text you want to render.
     *
     * @var string
     */
    private $_text;

    /**
     * Resource ID of the image canvas.
     *
     * @var resource
     */
    private $_img;

    /**
     * Tokens (each word).
     *
     * @var array
     */
    private $_tokens = array();

    /**
     * Fullpath to the font.
     *
     * @var string
     */
    private $_font;

    /**
     * Contains the bbox of each rendered lines.
     *
     * @var array
     */
    private $_bbox = array();

    /**
     * Defines in which mode the canvas has be set.
     *
     * @var array
     */
    private $_mode = '';

    /**
     * Color indices returned by imagecolorallocatealpha.
     *
     * @var array
     */
    private $_colors = array();

    /**
     * Width and height of the (rendered) text.
     *
     * @var array
     */
    private $_realTextSize = array('width' => false, 'height' => false);

    /**
     * Measurized lines.
     *
     * @var array
     */
    private $_lines = false;

    /**
     * Fontsize for which the last measuring process was done.
     *
     * @var array
     */
    private $_measurizedSize = false;

    /**
     * Is the text object initialized?
     *
     * @var bool
     */
    private $_init = false;

    /**
     * Constructor
     *
     * Set the text and options. This initializes a new Image_Text object. You must
     * set your text here. Optionally you can set all options here using the $options
     * parameter. If you finished switching all options you have to call the init()
     * method first before doing anything further! See Image_Text::set() for further
     * information.
     *
     * @param string $text    Text to print.
     * @param array  $options Options.
     *
     * @see Image_Text::set(), Image_Text::construct(), Image_Text::init()
     */
    public function __construct($text, $options = null)
    {
        $this->set('text', $text);
        if (!empty($options)) {
            $this->_options = array_merge($this->_options, $options);
        }
    }

    /**
     * Construct and initialize an Image_Text in one step.
     * This method is called statically and creates plus initializes an Image_Text
     * object. Beware: You will have to recall init() if you set an option afterwards
     * manually.
     *
     * @param string $text    Text to print.
     * @param array  $options Options.
     *
     * @return Image_Text
     * @see Image_Text::set(), Image_Text::Image_Text(), Image_Text::init()
     */
    public static function construct($text, $options)
    {
        $itext = new Image_Text($text, $options);
        $itext->init();
        return $itext;
    }

    /**
     * Set options
     *
     * Set a single or multiple options. It may happen that you have to reinitialize
     * the Image_Text object after changing options. For possible options, please
     * take a look at the class options array!
     *
     * @param array|string $option A single option name or the options array.
     * @param mixed        $value  Option value if $option is string.
     *
     * @return void
     * @see    Image_Text::Image_Text()
     * @throws Image_Text_Exception setting the value failed
     */
    public function set($option, $value = null)
    {
        $reInits = array_flip($this->_reInits);
        if (!is_array($option)) {
            if (!isset($value)) {
                throw new Image_Text_Exception('No value given.');
            }
            $option = array($option => $value);
        }
        foreach ($option as $opt => $val) {
            switch ($opt) {
            case 'color':
                $this->setColors($val);
                break;
            case 'text':
                if (is_array($val)) {
                    $this->_text = implode('\n', $val);
                } else {
                    $this->_text = $val;
                }
                break;
            default:
                $this->_options[$opt] = $val;
                break;
            }
            if (isset($reInits[$opt])) {
                $this->_init = false;
            }
        }
    }

    /**
     * Set the color-set
     *
     * Using this method you can set multiple colors for your text. Use a simple
     * numeric array to determine their order and give it to this function. Multiple
     * colors will be cycled by the options specified 'color_mode' option. The given
     * array will overwrite the existing color settings!
     *
     * The following colors syntaxes are understood by this method:
     * <ul>
     * <li>"#ffff00" hexadecimal format (HTML style), with and without #.</li>
     * <li>"#08ffff00" hexadecimal format (HTML style) with alpha channel (08),
     * with and without #.</li>
     * <li>array with 'r','g','b' and (optionally) 'a' keys, using int values.</li>
     * </ul>
     * A single color or an array of colors are allowed here.
     *
     * @param array|string $colors Single color or array of colors.
     *
     * @return void
     * @see Image_Text::setColor(), Image_Text::set()
     * @throws Image_Text_Exception
     */
    public function setColors($colors)
    {
        $i = 0;
        if (is_array($colors) && (is_string($colors[0]) || is_array($colors[0]))) {
            foreach ($colors as $color) {
                $this->setColor($color, $i++);
            }
        } else {
            $this->setColor($colors, $i);
        }
    }

    /**
     * Set a color
     *
     * This method is used to set a color at a specific color ID inside the color
     * cycle.
     *
     * The following colors syntaxes are understood by this method:
     * <ul>
     * <li>"#ffff00" hexadecimal format (HTML style), with and without #.</li>
     * <li>"#08ffff00" hexadecimal format (HTML style) with alpha channel (08), with
     * and without #.</li>
     * <li>array with 'r','g','b' and (optionally) 'a' keys, using int values.</li>
     * </ul>
     *
     * @param array|string $color Color value.
     * @param int          $id    ID (in the color array) to set color to.
     *
     * @return void
     * @see Image_Text::setColors(), Image_Text::set()
     * @throws Image_Text_Exception
     */

    function setColor($color, $id = 0)
    {
        if (is_array($color)) {
            if (isset($color['r']) && isset($color['g']) && isset($color['b'])) {
                $color['a'] = isset($color['a']) ? $color['a'] : 0;
                $this->_options['colors'][$id] = $color;
            } else if (isset($color[0]) && isset($color[1]) && isset($color[2])) {
                $color['r'] = $color[0];
                $color['g'] = $color[1];
                $color['b'] = $color[2];
                $color['a'] = isset($color[3]) ? $color[3] : 0;
                $this->_options['colors'][$id] = $color;
            } else {
                throw new Image_Text_Exception(
                    'Use keys 1,2,3 (optionally) 4 or r,g,b and (optionally) a.'
                );
            }
        } elseif (is_string($color)) {
            $color = $this->convertString2RGB($color);
            if ($color) {
                $this->_options['color'][$id] = $color;
            } else {
                throw new Image_Text_Exception('Invalid color.');
            }
        }
        if ($this->_img) {
            $aaFactor = ($this->_options['antialias']) ? 1 : -1;
            if (function_exists('imagecolorallocatealpha') && isset($color['a'])) {
                $this->_colors[$id] = $aaFactor *
                    imagecolorallocatealpha(
                        $this->_img,
                        $color['r'], $color['g'], $color['b'], $color['a']
                    );
            } else {
                $this->_colors[$id] = $aaFactor *
                    imagecolorallocate(
                        $this->_img,
                        $color['r'], $color['g'], $color['b']
                    );
            }
            if ($this->_colors[$id] == 0 && $aaFactor == -1) {
                // correction for black with antialiasing OFF
                // since color cannot be negative zero
                $this->_colors[$id] = -256;
            }
        }
    }

    /**
     * Initialize the Image_Text object.
     *
     * This method has to be called after setting the options for your Image_Text
     * object. It initializes the canvas, normalizes some data and checks important
     * options. Be sure to check the initialization after you switched some options.
     * The set() method may force you to reinitialize the object.
     *
     * @return void
     * @see Image_Text::set()
     * @throws Image_Text_Exception
     */
    public function init()
    {
        // Does the fontfile exist and is readable?
        $fontFile = rtrim($this->_options['font_path'], '/\\');
        $fontFile .= defined('OS_WINDOWS') && OS_WINDOWS ? '\\' : '/';
        $fontFile .= $this->_options['font_file'];
        $fontFile = realpath($fontFile);

        if (empty($fontFile)) {
            throw new Image_Text_Exception('You must supply a font file.');
        } elseif (!file_exists($fontFile)) {
            throw new Image_Text_Exception('Font file was not found.');
        } elseif (!is_readable($fontFile)) {
            throw new Image_Text_Exception('Font file is not readable.');
        } elseif (!imagettfbbox(1, 1, $fontFile, 1)) {
            throw new Image_Text_Exception('Font file is not valid.');
        }
        $this->_font = $fontFile;

        // Is the font size to small?
        if ($this->_options['width'] < 1) {
            throw new Image_Text_Exception('Width too small. Has to be > 1.');
        }

        // Check and create canvas
        $image_canvas = false;
        switch (true) {

        case (empty($this->_options['canvas'])):
            // Create new image from width && height of the clipping
            $this->_img = imagecreatetruecolor(
                $this->_options['width'], $this->_options['height']
            );
            if (!$this->_img) {
                throw new Image_Text_Exception('Could not create image canvas.');
            }
            break;

        case (is_resource($this->_options['canvas']) &&
            get_resource_type($this->_options['canvas']) == 'gd'):
            // The canvas is an image resource
            $image_canvas = true;
            $this->_img = $this->_options['canvas'];
            break;

        case (is_array($this->_options['canvas']) &&
            isset($this->_options['canvas']['width']) &&
            isset($this->_options['canvas']['height'])):

            // Canvas must be a width and height measure
            $this->_img = imagecreatetruecolor(
                $this->_options['canvas']['width'],
                $this->_options['canvas']['height']
            );
            break;

        case (is_array($this->_options['canvas']) &&
            isset($this->_options['canvas']['size']) &&
            ($this->_options['canvas']['size'] = 'auto')):

        case (is_string($this->_options['canvas']) &&
            ($this->_options['canvas'] = 'auto')):
            $this->_mode = 'auto';
            break;

        default:
            throw new Image_Text_Exception('Could not create image canvas.');
        }

        if ($this->_img) {
            $this->_options['canvas'] = array();
            $this->_options['canvas']['width'] = imagesx($this->_img);
            $this->_options['canvas']['height'] = imagesy($this->_img);
        }

        if ($this->_options['enable_alpha']) {
            imagesavealpha($this->_img, true);
            imagealphablending($this->_img, false);
        }

        if ($this->_options['background_color'] === null) {
            $this->_options['enable_alpha'] = true;
            imagesavealpha($this->_img, true);
            imagealphablending($this->_img, false);
            $colBg = imagecolorallocatealpha($this->_img, 255, 255, 255, 127);
        } else {
            $arBg = $this->convertString2RGB($this->_options['background_color']);
            if ($arBg === false) {
                throw new Image_Text_Exception('Background color is invalid.');
            }
            $colBg = imagecolorallocatealpha(
                $this->_img, $arBg['r'], $arBg['g'], $arBg['b'], $arBg['a']
            );
        }
        if ($image_canvas === false) {
            imagefilledrectangle(
                $this->_img,
                0, 0,
                $this->_options['canvas']['width'] - 1,
                $this->_options['canvas']['height'] - 1,
                $colBg
            );
        }

        // Save and repair angle
        $angle = $this->_options['angle'];
        while ($angle < 0) {
            $angle += 360;
        }
        if ($angle > 359) {
            $angle = $angle % 360;
        }
        $this->_options['angle'] = $angle;

        // Set the color values
        $this->setColors($this->_options['color']);

        $this->_lines = null;

        // Initialization is complete
        $this->_init = true;
    }

    /**
     * Auto measurize text
     *
     * Automatically determines the greatest possible font size to fit the text into
     * the text box. This method may be very resource intensive on your webserver. A
     * good tweaking point are the $start and $end parameters, which specify the
     * range of font sizes to search through. Anyway, the results should be cached if
     * possible. You can optionally set $start and $end here as a parameter or the
     * settings of the options array are used.
     *
     * @param int|bool $start Fontsize to start testing with.
     * @param int|bool $end   Fontsize to end testing with.
     *
     * @return int Fontsize measured
     * @see  Image_Text::measurize()
     * @throws Image_Text_Exception
     * @todo Beware of initialize
     */
    public function autoMeasurize($start = false, $end = false)
    {
        if (!$this->_init) {
            throw new Image_Text_Exception('Not initialized. Call ->init() first!');
        }

        $start = (empty($start)) ? $this->_options['min_font_size'] : $start;
        $end = (empty($end)) ? $this->_options['max_font_size'] : $end;

        // Run through all possible font sizes until a measurize fails
        // Not the optimal way. This can be tweaked!
        for ($i = $start; $i <= $end; $i++) {
            $this->_options['font_size'] = $i;
            $res = $this->measurize();

            if ($res === false) {
                if ($start == $i) {
                    $this->_options['font_size'] = -1;
                    throw new Image_Text_Exception("No possible font size found");
                }
                $this->_options['font_size'] -= 1;
                $this->_measurizedSize = $this->_options['font_size'];
                break;
            }
            // Always the last couple of lines is stored here.
            $this->_lines = $res;
        }
        return $this->_options['font_size'];
    }

    /**
     * Measurize text into the text box
     *
     * This method makes your text fit into the defined textbox by measurizing the
     * lines for your given font-size. You can do this manually before rendering (or
     * use even Image_Text::autoMeasurize()) or the renderer will do measurizing
     * automatically.
     *
     * @param bool $force Optionally, default is false, set true to force
     *                    measurizing.
     *
     * @return array Array of measured lines.
     * @see    Image_Text::autoMeasurize()
     * @throws Image_Text_Exception
     */
    public function measurize($force = false)
    {
        if (!$this->_init) {
            throw new Image_Text_Exception('Not initialized. Call ->init() first!');
        }
        $this->_processText();

        // Precaching options
        $font = $this->_font;
        $size = $this->_options['font_size'];

        $space = (1 + $this->_options['line_spacing'])
            * $this->_options['font_size'];

        $max_lines = (int)$this->_options['max_lines'];

        if (($max_lines < 1) && !$force) {
            return false;
        }

        $block_width = $this->_options['width'];
        $block_height = $this->_options['height'];

        $colors_cnt = sizeof($this->_colors);

        $text_line = '';

        $lines_cnt = 0;

        $lines = array();

        $text_height = 0;
        $text_width = 0;

        $i = 0;
        $para_cnt = 0;
        $width = 0;

        $beginning_of_line = true;

        // Run through tokens and order them in lines
        foreach ($this->_tokens as $token) {
            // Handle new paragraphs
            if ($token == "\n") {
                $bounds = imagettfbbox($size, 0, $font, $text_line);
                if ((++$lines_cnt >= $max_lines) && !$force) {
                    return false;
                }
                if ($this->_options['color_mode'] == 'paragraph') {
                    $c = $this->_colors[$para_cnt % $colors_cnt];
                    $i++;
                } else {
                    $c = $this->_colors[$i++ % $colors_cnt];
                }
                $lines[] = array(
                    'string' => $text_line,
                    'width' => $bounds[2] - $bounds[0],
                    'height' => $bounds[1] - $bounds[7],
                    'bottom_margin' => $bounds[1],
                    'left_margin' => $bounds[0],
                    'color' => $c
                );
                $text_width = max($text_width, ($bounds[2] - $bounds[0]));
                $text_height += (int)$space;
                if (($text_height > $block_height) && !$force) {
                    return false;
                }
                $para_cnt++;
                $text_line = '';
                $beginning_of_line = true;
                continue;
            }

            // Usual lining up

            if ($beginning_of_line) {
                $text_line = '';
                $text_line_next = $token;
                $beginning_of_line = false;
            } else {
                $text_line_next = $text_line . ' ' . $token;
            }
            $bounds = imagettfbbox($size, 0, $font, $text_line_next);
            $prev_width = isset($prev_width) ? $width : 0;
            $width = $bounds[2] - $bounds[0];

            // Handling of automatic new lines
            if ($width > $block_width) {
                if ((++$lines_cnt >= $max_lines) && !$force) {
                    return false;
                }
                if ($this->_options['color_mode'] == 'line') {
                    $c = $this->_colors[$i++ % $colors_cnt];
                } else {
                    $c = $this->_colors[$para_cnt % $colors_cnt];
                    $i++;
                }

                $lines[] = array(
                    'string' => $text_line,
                    'width' => $prev_width,
                    'height' => $bounds[1] - $bounds[7],
                    'bottom_margin' => $bounds[1],
                    'left_margin' => $bounds[0],
                    'color' => $c
                );
                $text_width = max($text_width, ($bounds[2] - $bounds[0]));
                $text_height += (int)$space;
                if (($text_height > $block_height) && !$force) {
                    return false;
                }

                $text_line = $token;
                $bounds = imagettfbbox($size, 0, $font, $text_line);
                $width = $bounds[2] - $bounds[0];
                $beginning_of_line = false;
            } else {
                $text_line = $text_line_next;
            }
        }
        // Store remaining line
        $bounds = imagettfbbox($size, 0, $font, $text_line);
        $i++;
        if ($this->_options['color_mode'] == 'line') {
            $c = $this->_colors[$i % $colors_cnt];
        } else {
            $c = $this->_colors[$para_cnt % $colors_cnt];
        }
        $lines[] = array(
            'string' => $text_line,
            'width' => $bounds[2] - $bounds[0],
            'height' => $bounds[1] - $bounds[7],
            'bottom_margin' => $bounds[1],
            'left_margin' => $bounds[0],
            'color' => $c
        );

        // add last line height, but without the line-spacing
        $text_height += $this->_options['font_size'];

        $text_width = max($text_width, ($bounds[2] - $bounds[0]));

        if (($text_height > $block_height) && !$force) {
            return false;
        }

        $this->_realTextSize = array(
            'width' => $text_width, 'height' => $text_height
        );
        $this->_measurizedSize = $this->_options['font_size'];

        return $lines;
    }

    /**
     * Render the text in the canvas using the given options.
     *
     * This renders the measurized text or automatically measures it first. The
     * $force parameter can be used to switch of measurizing problems (this may cause
     * your text being rendered outside a given text box or destroy your image
     * completely).
     *
     * @param bool $force Optional, initially false, set true to silence measurize
     *                    errors.
     *
     * @return void
     * @throws Image_Text_Exception
     */
    public function render($force = false)
    {
        if (!$this->_init) {
            throw new Image_Text_Exception('Not initialized. Call ->init() first!');
        }

        if (!$this->_tokens) {
            $this->_processText();
        }

        if (empty($this->_lines)
            || ($this->_measurizedSize != $this->_options['font_size'])
        ) {
            $this->_lines = $this->measurize($force);
        }
        $lines = $this->_lines;

        if ($this->_mode === 'auto') {
            $this->_img = imagecreatetruecolor(
                $this->_realTextSize['width'],
                $this->_realTextSize['height']
            );
            if (!$this->_img) {
                throw new Image_Text_Exception('Could not create image canvas.');
            }
            $this->_mode = '';
            $this->setColors($this->_options['color']);
        }

        $block_width = $this->_options['width'];

        $max_lines = $this->_options['max_lines'];

        $angle = $this->_options['angle'];
        $radians = round(deg2rad($angle), 3);

        $font = $this->_font;
        $size = $this->_options['font_size'];

        $line_spacing = $this->_options['line_spacing'];

        $align = $this->_options['halign'];

        $offset = $this->_getOffset();

        $start_x = $offset['x'];
        $start_y = $offset['y'];

        $sinR = sin($radians);
        $cosR = cos($radians);

        switch ($this->_options['valign']) {
        case self::IMAGE_TEXT_ALIGN_TOP:
            $valign_space = 0;
            break;
        case self::IMAGE_TEXT_ALIGN_MIDDLE:
            $valign_space = ($this->_options['height']
                    - $this->_realTextSize['height']) / 2;
            break;
        case self::IMAGE_TEXT_ALIGN_BOTTOM:
            $valign_space = $this->_options['height']
                - $this->_realTextSize['height'];
            break;
        default:
            $valign_space = 0;
        }

        $space = (1 + $line_spacing) * $size;

        // Adjustment of align + translation of top-left-corner to bottom-left-corner
        // of first line
        $new_posx = $start_x + ($sinR * ($valign_space + $size));
        $new_posy = $start_y + ($cosR * ($valign_space + $size));

        $lines_cnt = min($max_lines, sizeof($lines));

        $bboxes = array();
        // Go thorugh lines for rendering
        for ($i = 0; $i < $lines_cnt; $i++) {

            // Calc the new start X and Y (only for line>0)
            // the distance between the line above is used
            if ($i > 0) {
                $new_posx += $sinR * $space;
                $new_posy += $cosR * $space;
            }

            // Calc the position of the 1st letter. We can then get the left and
            // bottom margins 'i' is really not the same than 'j' or 'g'.
            $left_margin = $lines[$i]['left_margin'];
            $line_width = $lines[$i]['width'];

            // Calc the position using the block width, the current line width and
            // obviously the angle. That gives us the offset to slide the line.
            switch ($align) {
            case self::IMAGE_TEXT_ALIGN_LEFT:
                $hyp = 0;
                break;
            case self::IMAGE_TEXT_ALIGN_RIGHT:
                $hyp = $block_width - $line_width - $left_margin;
                break;
            case self::IMAGE_TEXT_ALIGN_CENTER:
                $hyp = ($block_width - $line_width) / 2 - $left_margin;
                break;
            default:
                $hyp = 0;
                break;
            }

            $posx = $new_posx + $cosR * $hyp;
            $posy = $new_posy - $sinR * $hyp;

            $c = $lines[$i]['color'];

            // Render textline
            $bboxes[] = imagettftext(
                $this->_img, $size, $angle, $posx, $posy,
                $c, $font, $lines[$i]['string']
            );
        }
        $this->_bbox = $bboxes;
    }

    /**
     * Return the image ressource.
     *
     * Get the image canvas.
     *
     * @return resource Used image resource
     */
    public function getImg()
    {
        return $this->_img;
    }

    /**
     * Display the image (send it to the browser).
     *
     * This will output the image to the users browser. You can use the standard
     * IMAGETYPE_* constants to determine which image type will be generated.
     * Optionally you can save your image to a destination you set in the options.
     *
     * @param bool $save Save or not the image on printout.
     * @param bool $free Free the image on exit.
     *
     * @return  bool         True on success
     * @see Image_Text::save()
     * @throws Image_Text_Exception
     */
    public function display($save = false, $free = false)
    {
        if (!headers_sent()) {
            header(
                "Content-type: " .
                image_type_to_mime_type($this->_options['image_type'])
            );
        } else {
            throw new Image_Text_Exception('Header already sent.');
        }
        switch ($this->_options['image_type']) {
        case IMAGETYPE_PNG:
            $imgout = 'imagepng';
            break;
        case IMAGETYPE_JPEG:
            $imgout = 'imagejpeg';
            break;
        case IMAGETYPE_BMP:
            $imgout = 'imagebmp';
            break;
        default:
            throw new Image_Text_Exception('Unsupported image type.');
        }
        if ($save) {
            $imgout($this->_img);
            $this->save();
        } else {
            $imgout($this->_img);
        }

        if ($free) {
            $res = imagedestroy($this->_img);
            if (!$res) {
                throw new Image_Text_Exception('Destroying image failed.');
            }
        }
    }

    /**
     * Save image canvas.
     *
     * Saves the image to a given destination. You can leave out the destination file
     * path, if you have the option for that set correctly. Saving is possible with
     * the display() method, too.
     *
     * @param bool|string $destFile The destination to save to (optional, uses
     *                              options value else).
     *
     * @see Image_Text::display()
     * @return void
     * @throws Image_Text_Exception
     */
    public function save($destFile = false)
    {
        if (!$destFile) {
            $destFile = $this->_options['dest_file'];
        }
        if (!$destFile) {
            throw new Image_Text_Exception("Invalid desitination file.");
        }

        switch ($this->_options['image_type']) {
        case IMAGETYPE_PNG:
            $imgout = 'imagepng';
            break;
        case IMAGETYPE_JPEG:
            $imgout = 'imagejpeg';
            break;
        case IMAGETYPE_BMP:
            $imgout = 'imagebmp';
            break;
        default:
            throw new Image_Text_Exception('Unsupported image type.');
            break;
        }

        $res = $imgout($this->_img, $destFile);
        if (!$res) {
            throw new Image_Text_Exception('Saving file failed.');
        }
    }

    /**
     * Get completely translated offset for text rendering.
     *
     * Get completely translated offset for text rendering. Important for usage of
     * center coords and angles.
     *
     * @return array Array of x/y coordinates.
     */
    private function _getOffset()
    {
        // Presaving data
        $width = $this->_options['width'];
        $height = $this->_options['height'];
        $angle = $this->_options['angle'];
        $x = $this->_options['x'];
        $y = $this->_options['y'];
        // Using center coordinates
        if (!empty($this->_options['cx']) && !empty($this->_options['cy'])) {
            $cx = $this->_options['cx'];
            $cy = $this->_options['cy'];
            // Calculation top left corner
            $x = $cx - ($width / 2);
            $y = $cy - ($height / 2);
            // Calculating movement to keep the center point on himslf after rotation
            if ($angle) {
                $ang = deg2rad($angle);
                // Vector from the top left cornern ponting to the middle point
                $vA = array(($cx - $x), ($cy - $y));
                // Matrix to rotate vector
                // sinus and cosinus
                $sin = round(sin($ang), 14);
                $cos = round(cos($ang), 14);
                // matrix
                $mRot = array(
                    $cos, (-$sin),
                    $sin, $cos
                );
                // Multiply vector with matrix to get the rotated vector
                // This results in the location of the center point after rotation
                $vB = array(
                    ($mRot[0] * $vA[0] + $mRot[2] * $vA[0]),
                    ($mRot[1] * $vA[1] + $mRot[3] * $vA[1])
                );
                // To get the movement vector, we subtract the original middle
                $vC = array(
                    ($vA[0] - $vB[0]),
                    ($vA[1] - $vB[1])
                );
                // Finally we move the top left corner coords there
                $x += $vC[0];
                $y += $vC[1];
            }
        }
        return array('x' => (int)round($x, 0), 'y' => (int)round($y, 0));
    }

    /**
     * Convert a color to an array.
     *
     * The following colors syntax must be used:
     * "#08ffff00" hexadecimal format with alpha channel (08)
     *
     * @param string $scolor string of colorcode.
     *
     * @see Image_Text::IMAGE_TEXT_REGEX_HTMLCOLOR
     * @return bool|array false if string can't be converted to array
     */
    public static function convertString2RGB($scolor)
    {
        if (preg_match(self::IMAGE_TEXT_REGEX_HTMLCOLOR, $scolor, $matches)) {
            return array(
                'r' => hexdec($matches[2]),
                'g' => hexdec($matches[3]),
                'b' => hexdec($matches[4]),
                'a' => hexdec(!empty($matches[1]) ? $matches[1] : 0),
            );
        }
        return false;
    }

    /**
     * Extract the tokens from the text.
     *
     * @return void
     */
    private function _processText()
    {
        if (!isset($this->_text)) {
            return;
        }
        $this->_tokens = array();

        // Normalize linebreak to "\n"
        $this->_text = preg_replace("[\r\n]", "\n", $this->_text);

        // Get each paragraph
        $paras = explode("\n", $this->_text);

        // loop though the paragraphs
        // and get each word (token)
        foreach ($paras as $para) {
            $words = explode(' ', $para);
            foreach ($words as $word) {
                $this->_tokens[] = $word;
            }
            // add a "\n" to mark the end of a paragraph
            $this->_tokens[] = "\n";
        }
        // we do not need an end paragraph as the last token
        array_pop($this->_tokens);
    }
}
