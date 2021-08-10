<?php
/**
 * Класс работы с изображениями
 * @version 4.0.0
 * @author Yuri Frantsevich (FYN)
 * Date 03/11/2006
 * @copyright 2006-2021
 */

namespace FYN;

class FImages {

    /**
     * Ширина создаваемого изображения
     * @var int
     */
    private $w;

    /**
     * Высота создаваемого изображения
     * @var int
     */
    private $h;

    /**
     * x-координата исходного изображения.
     * @var int
     */
    private $dx = 0;

    /**
     * y-координата исходного изображения
     * @var int
     */
    private $dy = 0;

    /**
     * Ширина исходного изображения
     * @var int
     */
    private $bw;

    /**
     * Ширина исходного изображения
     * @var int
     */
    private $bh;

    /**
     * Ресурс исходного изображения
     * @var mixed
     */
    private $src;

    /**
     * Ресурс целевого изображения
     * @var mixed
     */
    private $dst;

    /**
     * Тип исходного изображения
     * @var string
     */
    private $fmime;

    /**
     * Mime тип создаваемого изображения
     * @var string
     */
    private $mime;

    /**
     * Тип создаваемого изображения
     * @var string
     */
    private $type;

    /**
     * Путь к исходному файлу
     * @var string
     */
    private $path;

    /**
     * Выводить или нет техт на конечном изображении
     * @var bool
     */
    private $text_line = false;

    /**
     * Параметры выводимого текста
     * @var array
     */
    private $text_line_data = array(
        'text' => 'Hello world!',
        'color' => array('red'=>255, 'green'=>255, 'blue'=>255),
        'font' => 4, // Может принимать значения 1, 2, 3, 4, 5 для встроенных шрифтов в кодировке latin2 (более высокое число соответствует большему шрифту) или любому изшрифтов, зарегистрированных с помощью imageloadfont()
        'size' => 12, // размер шрифта в типографских пунктах
        'angle' => 0, // Угол в градусах, 0 градусов означает расположение текста слева направо. Положительные значения означают поворот текста против часовой стрелки. Например, текст повернутый на 90 градусов нужно будет читать снизу вверх.
        'coordinates' => array(0, 0) // координаты начальной точки
    );

    /**
     * Как пеобразовать изображение на выводе
     * @var
     */
    private $flip = false;

    /**
     * Наложение части другого изображения
     * "Водяной знак"
     * @var bool
     */
    private $merge = false;

    /**
     * Параметры для наложения изображения
     * @var array
     */
    private $merge_data = array(
        'src' => '', // Путь к изображению которое накладываем на исходное
        'x' => 0,   // x-координата результирующего изображения.
        'y' => 0,   // y-координата результирующего изображения.
    );

    /**
     * Отладка, вывод ошибок на экран
     * @var int
     */
    private $debug = 0;

    /**
     * Логи
     * @var array
     */
    private $logs = array();

    /**
     * Имя файла в который сохраняется лог
     * @var string
     */
    private $log_file = 'images.log';

    /**
     * Включать или нет кеширование в браузере для изображения
     * @var bool
     */
    private $cash = false;

    /**
     * Время кеширования изображения (для браузера)
     * @var int
     */
    private $LifeTime = 86400; //One day

    /**
     * Префикс для имени временного файла
     * @var string
     */
    private $prefix = 'MYIMG';

    /**
     * Высота исходного файла при заполнении
     * @var int
     */
    private $wf;

    /**
     * Ширина исходного файла при заполнении
     * @var int
     */
    private $hf;

    /**
     * Путь к временному файлу
     * @var string
     */
    private $tmp_path;

    /**
     * Капча (случайный текст)
     * @var mixed
     */
    private $key_string;

    /**
     * Ширина капчи
     * @var int
     */
    private $captcha_width = 120;

    /**
     * Высота капчи
     * @var int
     */
    private $captcha_height = 60;

    /**
     * Папка с картинками шрифтов
     * @var string
     */
    private $captcha_fontsdir = 'fonts';

    /**
     * Используемые символы для капчи
     * @var string
     */
    private $captcha_allowed_symbols = "0123456789abcdeghkmnpqsuvxyz";

    /**
     * Количество символов в капче
     */
    private $captcha_length = 6;
    //private $captcha_length = mt_rand(5,6); # random 5 or 6

    /**
     * Текст для подписи капчи
     */
    private $captcha_credits = '© FYN'; //SERVER_NAME; # if empty, HTTP_HOST will be shown

    /**
     * Выводить или нет под капчей строку подписи капчи
     */
    private $captcha_show_credits = true; # set to false to remove credits line. Credits adds 12 pixels to image height

    /**
     * Использовать ли пробелы между символами
     */
    private $captcha_no_spaces = true; # increase safety by prevention of spaces between symbols

    /**
     * Вертикальная разбежка символов
     */
    private $captcha_amplitude = 5; # symbol's vertical fluctuation amplitude divided by 2

    /**
     * Качество картинки капчи
     * @var integer
     */
    private $captcha_jpeg_quality = 90;

    /**
     * FImages constructor.
     *
     * @param mixed $type - тип создаваемого изображения
     * @param int $w - ширина изображения
     * @param int $h - высота изображения
     */
    public function __construct ($type = false, $w=0, $h=0) {
        if (defined('IMG_W') && !$w) $w = IMG_W;
        if (defined('IMG_H') && !$h) $h = IMG_H;
        if (defined('IMG_TYPE') && !$type) $type = IMG_TYPE;
        if (defined('IMG_DEBUG')) $this->debug = IMG_DEBUG;
        if (defined('IMG_LOG_NAME')) $this->log_file = IMG_LOG_NAME;
        if ($w) $this->w = $w;
        if ($h) $this->h = $h;
        $type = strtoupper($type);
        if ($type && ($type == 'GIF' || $type == 'JPG' || $type == 'JPEG' || $type == 'PNG' || $type == 'WEBP')) {
            if ($type == 'JPEG') $type = 'JPG';
            $this->type = $type;
        }
        else $this->type = 'PNG';
        if ($this->type == 'JPG') $this->mime = 'image/pjpeg';
        elseif ($this->type == 'PNG') $this->mime = 'image/png';
        elseif ($this->type == 'GIF') $this->mime = 'image/gif';
        elseif ($this->type == 'WEBP') $this->mime = 'image/webp';
        if (!defined('TMP_DIR')) {
            /**
             * Временная директория для создания файла
             */
            $tmp_dir = (isset($_ENV['TMP']) && $_ENV['TMP'])?$_ENV['TMP']:'';
            if (!$tmp_dir) $tmp_dir = (isset($_ENV['TMPDIR']) && $_ENV['TMPDIR'])?$_ENV['TMPDIR']:'/tmp';
            define('TMP_DIR', $tmp_dir);
        }
        return true;
    }

    /**
     * Деструктор класса.
     */
    public function __destruct() {
    }

    /**
     * Создание файла с пропорциональным изменением размера
     * Если соотношение сторон $w/$h не совпадают с соотношением сторон изображения, то используются соотношения сторон изображения
     * При этом изображение будет вписываться в заданные параметры ширины и высоты
     * @param $path - путь к файлу
     * @param integer $save - куда и как сохранить/вывести файл (см. output)
     * @param int $w - ширина
     * @param int $h - высота
     * @return bool|string
     */
    public function resize_pro ($path, $save=0, $w=0, $h=0) {
        $this->dx = 0;
        $this->dy = 0;
        $this->bw = 0;
        $this->bh = 0;
        $image = false;
        if ($this->ex_file($path)) {
            $this->w = ($w)?$w:$this->w;
            $this->h = ($h)?$h:$this->h;
            $this->getSize();
            $this->getReSize();
            $this->createResizeImage();
            $image = $this->output($save);
        }
        return $image;
    }

    /**
     * Создать файл с обрезкой размера под заданные параметры
     * Если соотношение сторон $w/$h не совпадают с соотношением сторон изображения, то изображение будет обрезано под заданные параметры по меньшему значению
     * То есть если ширина равна $w, а пропорциональная высота должна быть больше, чем $h, то изображение обрезается до высоты $h. И наоборот.
     * @param $path - путь к файлу
     * @param integer $save - куда и как сохранить/вывести файл (см. output)
     * @param int $w - ширина
     * @param int $h - высота
     * @return bool|string
     */
    public function cut ($path, $save=0, $w=0, $h=0) {
        $this->dx = 0;
        $this->dy = 0;
        $this->bw = 0;
        $this->bh = 0;
        $image = false;
        if ($this->ex_file($path)) {
            $this->getSize();
            $this->w = ($w)?$w:$this->w;
            $this->h = ($h)?$h:$this->h;
            $this->getCutSize();
            $this->createResizeImage();
            $image = $this->output($save);
        }
        return $image;
    }

    /**
     * Создать файл с растягиванием под заданное соотношение сторон
     * @param $path - путь к файлу
     * @param integer $save - куда и как сохранить/вывести файл (см. output)
     * @param int $w - ширина
     * @param int $h - высота
     * @return bool|string
     */
    public function resize ($path, $save=0, $w=0, $h=0) {
        $this->dx = 0;
        $this->dy = 0;
        $this->bw = 0;
        $this->bh = 0;
        $image = false;
        if ($this->ex_file($path)) {
            $this->getSize();
            $this->w = ($w)?$w:$this->w;
            $this->h = ($h)?$h:$this->h;
            $this->createResizeImage();
            $image = $this->output($save);
        }
        return $image;
    }

    /**
     * Создать файл с заливкой исходным изображением
     * @param $path - путь к файлу
     * @param integer $save - куда и как сохранить/вывести файл (см. output)
     * @param int $w - ширина
     * @param int $h - высота
     * @return bool|string
     */
    public function fill ($path, $save=0, $w=0, $h=0) {
        $this->dx = 0;
        $this->dy = 0;
        $this->bw = 0;
        $this->bh = 0;
        $image = false;
        if ($this->ex_file($path)) {
            $this->w = ($w)?$w:$this->w;
            $this->h = ($h)?$h:$this->h;
            $this->hf = $this->h;
            $this->wf = $this->w;
            $this->getSize();
            $this->getReSize();
            $this->createResizeImage();
            $this->createFillImage();
            $image = $this->output($save);
        }
        return $image;
    }

    /**
     * Проверка существования файла и опредениение его типа
     * @param $path - путь к файлу
     * @return bool
     */
    private function ex_file ($path) {
        if (file_exists($path)) {
            $this->path = $path;
            list($width, $height, $type, $attr) = getimagesize($this->path);
            unset($width, $height, $attr);
            if ($type == 1) $this->fmime = 'GIF';
            elseif ($type == 2) $this->fmime = 'JPG';
            elseif ($type == 3) $this->fmime = 'PNG';
            elseif ($type == 4) $this->fmime = 'SWF';
            else {
                if (!$type) $type = 'Unknown file format';
                $message = 'Wrong file format: ('.$type.') "'.$this->path.'"';
                $this->img_error($message);
                return false;
            }
            if ($this->fmime == 'JPG' || $this->fmime == 'SWF') {
                $this->src = ImageCreateFromJpeg($this->path);
            }
            elseif ($this->fmime == 'GIF') {
                $this->src = ImageCreateFromGIF($this->path);
            }
            elseif ($this->fmime = 'PNG') {
                $this->src = ImageCreateFromPNG($this->path);
            }
            return true;
        }
        else {
            $message = 'File "'.$this->path.'" not exists';
            $this->img_error($message);
            return false;
        }
    }

    /**
     * Добавить надпись на изображение
     * @param $text - текст надписи
     * @param $text_data - параметры надписи
     * array(
     *     'text' => 'Hello world!',
     *     'color' => array('red'=>255, 'green'=>255, 'blue'=>255),
     *     'font' => 4, // Может принимать значения 1, 2, 3, 4, 5 для встроенных шрифтов в кодировке latin2 (более высокое число соответствует большему шрифту) или любому изшрифтов, зарегистрированных с помощью imageloadfont()
     *     'size' => 12, // размер шрифта в типографских пунктах
     *     'angle' => 0, // Угол в градусах, 0 градусов означает расположение текста слева направо. Положительные значения означают поворот текста против часовой стрелки. Например, текст повернутый на 90 градусов нужно будет читать снизу вверх.
     *     'coordinates' => array(0, 0) // координаты начальной точки
     * );
     */
    public function setTextLine ($text, $text_data = array()) {
        if ($text) {
            $this->text_line = true;
            $this->text_line_data['text'] = $text;
            if (isset($text_data['color']) && is_array($text_data['color'])) $this->text_line_data['color'] = $text_data['color'];
            else $this->text_line_data['color'] = array('red'=>255, 'green'=>255, 'blue'=>255);
            if (isset($text_data['font']) && $text_data['font']) {
                if (is_int($text_data['font']) && $text_data['font'] > 0) $this->text_line_data['font'] = $text_data['font'];
                elseif ($text_data['font'] && file_exists($text_data['font'])) $this->text_line_data['font'] = $text_data['font'];
            }
            if (isset($text_data['coordinates']) && is_array($text_data['coordinates'])) $this->text_line_data['coordinates'] = $text_data['coordinates'];
            if (isset($text_data['size']) && $text_data['size']) $this->text_line_data['size'] = $text_data['size']*1;
            if (isset($text_data['angle']) && $text_data['angle']) $this->text_line_data['angle'] = $text_data['angle']*1;
            else $this->text_line_data['angle'] = 0;
            /**
            $text_line_data = array(
            'text' => 'Hello world!',
            'color' => array('red'=>255, 'green'=>255, 'blue'=>255),
            'font' => 'fonts/arial.ttf', // Может принимать значения 1, 2, 3, 4, 5 для встроенных шрифтов в кодировке latin2 (более высокое число соответствует большему шрифту) или любому изшрифтов, зарегистрированных с помощью imageloadfont()
            'coordinates' => array(2, 2) // координаты начальной точки
            );
             */
        }
        else $this->text_line = false;
    }

    /**
     * Парамерты "водяного знака" который наносится на конечное изображение
     * @param $src - Путь к изображению которое накладываем на исходное
     * @param array $merge_data - координаты точки на конечном изображении для "водяного знака"
     * array(
     *     'x' => 0, // x-координата результирующего изображения
     *     'y' => 0, // y-координата результирующего изображения
     * );
     */
    public function setMerge ($src, $merge_data = array()) {
        if ($src && file_exists($src)) {
            if (isset($merge_data['x'])) $this->merge_data['x'] = $merge_data['x'];
            if (isset($merge_data['y'])) $this->merge_data['y'] = $merge_data['y'];
            $this->merge_data['src'] = $src;
            $this->merge = true;
        }
        else $this->merge = false;
    }

    /**
     * Поворот конечного изображения
     * @param $flip - как повернуть 'HORIZONTAL' - горизонтально,'VERTICAL' - вертикально,'BOTH' - и горизонтально и вертикально
     */
    public function setFlip ($flip) {
        if (in_array(strtoupper($flip), array('HORIZONTAL','BOTH','VERTICAL'))) $this->flip = $flip;
        else $this->flip = false;
    }

    /**
     * Создание изображения
     * @param $save - куда и как сохранить/вывести файл
     *      $save = 1 // replace old image and return this file as string
     *      $save = 2 // print new image
     *      $save = 3 // return new file as string
     *      $save != 1 or 2 or 3 //return descriptor of new file;
     * @return bool|string
     */
    private function output ($save) {
        $dst_w = imagesx($this->dst);
        $dst_h = imagesy($this->dst);
        // вращение изображения (устанавливается в функции setFlip)
        if ($this->flip && in_array(strtoupper($this->flip), array('HORIZONTAL','BOTH','VERTICAL'))) {
            $mode = IMG_FLIP_HORIZONTAL;
            if (strtoupper($this->flip) == 'BOTH') $mode = IMG_FLIP_BOTH;
            elseif (strtoupper($this->flip) == 'VERTICAL') $mode = IMG_FLIP_VERTICAL;
            imageflip($this->dst, $mode);
        }
        // текст на изображении (устанавливается в функции setTextLine)
        if ($this->text_line) {
            $x = $this->text_line_data['coordinates'][0];
            $y = $this->text_line_data['coordinates'][1];
            $a = $this->text_line_data['angle'];
            if ($a > 360 || $a < -360) $a = 0;
            if ($x > $dst_w) {
                if (($a >= 0 && $a <= 90) || ($a <= -270 && $a >= -360)) $x = 4;
                elseif (($a > 90 && $a <= 180) || ($a <= -180 && $a > -270)) $x = $dst_w-4;
                elseif (($a > 180 && $a <= 270) || ($a <= -90 && $a > -180)) $x = $dst_w-4;
                else $x = 4;
            }
            if ($y > $dst_h) {
                if (($a >= 0 && $a <= 90) || ($a <= -270 && $a >= -360)) $y = $dst_h-4;
                elseif (($a > 90 && $a <= 180) || ($a <= -180 && $a > -270)) $y = 4;
                elseif (($a > 180 && $a <= 270) || ($a <= -90 && $a > -180)) $y = 4;
                else $y = $dst_h-4;
            }
            $text_color = imagecolorallocate($this->dst, $this->text_line_data['color']['red'], $this->text_line_data['color']['green'], $this->text_line_data['color']['blue']);
            if (is_string($this->text_line_data['font']) && file_exists($this->text_line_data['font'])) {
                imagettftext($this->dst, $this->text_line_data['size'], $a, $x, $y, $text_color, $this->text_line_data['font'], $this->text_line_data['text']);
            }
            else {
                $font = 2;
                if (is_int($this->text_line_data['font']) && $this->text_line_data['font'] > 0) $font = $this->text_line_data['font'];
                imagestring($this->dst, $font, $x, $y, $this->text_line_data['text'], $text_color);
            }
        }
        // "водяной знак" на изображении (устанавливается в функции setMerge)
        if ($this->merge) {
            $src = '';
            $src_data = getimagesize($this->merge_data['src']);
            if ($src_data[2] == IMAGETYPE_GIF) $src = imagecreatefromgif($this->merge_data['src']);
            elseif ($src_data[2] == IMAGETYPE_PNG) $src = imagecreatefrompng($this->merge_data['src']);
            elseif ($src_data[2] == IMAGETYPE_JPEG || $src_data[2] == IMAGETYPE_JPEG2000) $src = imagecreatefromjpeg($this->merge_data['src']);
            if ($src) {
                $dst_x = $this->merge_data['x'];
                $dst_y = $this->merge_data['y'];
                $src_w = imagesx($src);
                $src_h = imagesy($src);
                if ($dst_x > $dst_w) $dst_x = (($dst_w - $src_w) > 0)?($dst_w - $src_w):0;
                if ($dst_y > $dst_h) $dst_y = (($dst_h - $src_h) > 0)?($dst_h - $src_h):0;
                $this->logs[] = "$dst_x > $dst_w && $dst_y > $dst_h";
                imagealphablending($src, TRUE);
                $img_w = imagesx($this->dst);
                $img_h = imagesy($this->dst);
                $new = imagecreatetruecolor($img_w, $img_h);
                imagecopy($new, $this->dst, 0, 0, 0, 0, $img_w, $img_h);
                imagedestroy($this->dst);
                $this->dst = $new;
                imagecopy($this->dst, $src, $dst_x, $dst_y, 0, 0, $src_w, $src_h);
            }
        }
        if ($save==1) {
            if ($this->type == 'JPG') ImageJPEG($this->dst, $this->path, 90);
            elseif ($this->type == 'PNG') ImagePNG($this->dst, $this->path);
            elseif ($this->type == 'GIF') {
                if (function_exists("ImageGIF")) ImageGIF($this->dst, $this->path);
                else ImageJPEG($this->dst, $this->path, 90);
            }
            elseif ($this->type == 'WEBP') {
                if (function_exists("imagewebp")) imagewebp($this->dst, $this->path, 90);
                else ImageJPEG($this->dst, $this->path, 90);
            }
            ImageDestroy($this->dst);
            $file = fopen($this->path,"rb");
            $image = fread($file, filesize($this->path));
            fclose($file);
            //if ($this->tmp_path) $this->deleteTmp();
            return $image;
        }
        elseif ($save == 2) {
            if ($this->tmp_path) $this->deleteTmp();
            header("Content-type: ".$this->mime);
            if ($this->cash) {
                header("Expires: " . gmdate("D, d M Y H:i:s",time()+$this->LifeTime) . " GMT");
                header("Last-Modified: " . gmdate("D, d M Y 00:00:00",time()-$this->LifeTime) . " GMT");
                header("Cache-Control: post-check=".$this->LifeTime.",pre-check=".$this->LifeTime);
                header("Cache-Control: max-age=".$this->LifeTime);
                header("Cache-Control: min-fresh=".$this->LifeTime);
            }
            if ($this->type == 'JPG') ImageJPEG($this->dst);
            elseif ($this->type == 'PNG') ImagePNG($this->dst);
            elseif ($this->type == 'GIF') {
                if (function_exists("ImageGIF")) ImageGIF($this->dst);
                else ImageJPEG($this->dst);
            }
            elseif ($this->type == 'WEBP') {
                if (function_exists("imagewebp")) imagewebp($this->dst);
                else ImageJPEG($this->dst);
            }

            ImageDestroy($this->dst);
            exit;
        }
        elseif ($save == 3) {
            $ext = ".".strtolower($this->type);
            $tmpname = @tempnam(TMP_DIR, $this->prefix); //'MYIMG'
            if (file_exists($tmpname)) unlink($tmpname);
            $tmpname = $tmpname.$ext;
            if ($this->type == 'JPG') ImageJPEG($this->dst, $tmpname, 90);
            elseif ($this->type == 'PNG') ImagePNG($this->dst, $tmpname);
            elseif ($this->type == 'GIF') {
                if (function_exists("ImageGIF")) ImageGIF($this->dst, $tmpname);
                else ImageJPEG($this->dst, $tmpname, 90);
            }
            elseif ($this->type == 'WEBP') {
                if (function_exists("imagewebp")) imagewebp($this->dst, $tmpname, 90);
                else ImageJPEG($this->dst, $tmpname, 90);
            }
            ImageDestroy($this->dst);
            $file = fopen($tmpname,"rb");
            $image = fread($file, filesize($tmpname));
            fclose($file);
            if (file_exists($tmpname)) unlink($tmpname);
            //if ($this->tmp_path) $this->deleteTmp();
            return $image;
        }
        else {
            return $this->dst;
        }
    }

    /**
     * Создание временного файла из исходника и получение данных о нём
     * @param $img - исходник картинки
     * @param mixed $function - функция обработки
     * @param integer $save - куда и как сохранить/вывести файл (см. output)
     * @param int $w - ширина
     * @param int $h - высота
     * @return mixed
     */
    public function getTempFile ($img, $function = false, $save = 0, $w=0, $h=0) {
        $ext = ".".strtolower($this->type);
        $this->tmp_path = tempnam(TMP_DIR, 'IMG');
        if (file_exists($this->tmp_path)) unlink($this->tmp_path);
        $this->tmp_path = $this->tmp_path.$ext;
        $file = fopen($this->tmp_path,"w");
        fwrite($file, $img);
        fclose($file);
        $row['Path'] = $this->tmp_path;
        $images = false;
        if ($function) {
            $function = strtoupper($function);
            if ($function == 'CUT') $images = $this->cut($this->tmp_path, $save, $w, $h);
            elseif ($function == 'FILL') $images = $this->fill($this->tmp_path, $save, $w, $h);
            elseif ($function == 'RESIZE') $images = $this->resize($this->tmp_path, $save, $w, $h);
            elseif ($function == 'RESIZE_PRO') $images = $this->resize_pro($this->tmp_path, $save, $w, $h);
            if ($images) $row['Image'] = $images;
        }
        return $row;
    }

    /**
     * Удалить временный файл
     * @param mixed $path - путь к временному файлу
     * @return bool
     */
    private function deleteTmp ($path = false) {
        if ($path) $this->tmp_path = $path;
        if (file_exists($this->tmp_path)) {
            unlink($this->tmp_path);
            $this->tmp_path = '';
            return true;
        }
        else return false;
    }

    /**
     * Получение размеров исходного изображения
     * @param bool $src
     */
    private function getSize ($src = false) {
        if (!$src) $src = $this->src;
        $this->bw = ImageSX($src);
        $this->bh = ImageSY($src);
        if (!$this->w) $this->w = $this->bw;
        if (!$this->h) $this->h = $this->bh;
    }

    /**
     * Создание изображения с изменёнными размерами
     */
    private function createResizeImage () {
        if (function_exists("ImageCreateTrueColor")) {
            $this->dst = ImageCreateTrueColor($this->w,$this->h);
            ImageCopyResampled($this->dst,$this->src,0,0,$this->dx,$this->dy,$this->w,$this->h,$this->bw,$this->bh);
        }
        else {
            $this->dst = ImageCreate($this->w,$this->h);
            ImageCopyResized($this->dst,$this->src,0,0,$this->dx,$this->dy,$this->w,$this->h,$this->bw,$this->bh);
        }
    }

    /**
     * Создание изображения с заполнением исходным изображением
     */
    private function createFillImage () {
        $color_index = ImageColorAt($this->dst, 1, 1);
        $color = ImageColorsForIndex($this->dst,$color_index);
        $dst_x = 0;
        $dst_y = 0;
        if ($this->wf > $this->w) $dst_x = ($this->wf - $this->w)/2;
        if ($this->hf > $this->h) $dst_y = ($this->hf - $this->h)/2;
        if (function_exists("ImageCreateTrueColor")) {
            $new_img = ImageCreateTrueColor($this->wf, $this->hf);
            $bgc = ImageColorAllocateAlpha($new_img, $color['red'], $color['green'], $color['blue'], $color['alpha']);
        }
        else {
            $new_img = ImageCreate($this->wf, $this->hf);
            $bgc = ImageColorAllocate($new_img, $color['red'], $color['green'], $color['blue']);
        }
        ImageFilledRectangle($new_img, 0, 0, $this->wf, $this->hf, $bgc);
        ImageCopy($new_img,$this->dst,$dst_x,$dst_y,0,0,$this->w,$this->h);
        $this->dst = $new_img;
    }

    /**
     * Создание копии изображения
     * @param string $path - путь к пкапке в которую сохраняем копию
     * @param mixed $name - имя нового файла
     * @return bool
     */
    public function createImgCopy ($path, $name=false) {
        $path = preg_replace("/\/$/", "", $path);
        if (!$name) {
            if (!($tmpname = tempnam($path, $this->prefix))) {
                $message = 'Can`t create files in directory '.$path.'.';
                $this->img_error($message);
                return false;
            }
            unlink ($tmpname);
            $endname = strtolower($this->type);
            $fullpath = preg_replace("/\.\w+$/", ".$endname", $tmpname);
        }
        else $fullpath = $path."/".$name;
        if ($this->type == 'JPG') {
            if (!(ImageJPEG($this->dst, $fullpath, 90))) {
                $message = 'Can`t create file '.$name.'.';
                $this->img_error($message);
                return false;
            }
        }
        elseif ($this->type == 'PNG') {
            if (!(ImagePNG($this->dst, $fullpath))) {
                $message = 'Can`t create file '.$name.'.';
                $this->img_error($message);
                return false;
            }
        }
        elseif ($this->type == 'GIF') {
            if (function_exists("ImageGIF")) {
                if (!(ImageGIF($this->dst, $fullpath))) {
                    $message = 'Can`t create file '.$name.'.';
                    $this->img_error($message);
                    return false;
                }
            }
            else {
                if (!(ImageJPEG($this->dst, $fullpath, 90))) {
                    $message = 'Can`t create file '.$name.'.';
                    $this->img_error($message);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Обработка ошибок
     * @param $message - сообщение об ошибке
     */
    private function img_error ($message) {
        $this->logs[] = $message;
        if ($this->tmp_path) $this->deleteTmp();
        if ($this->debug) {
            $msg ="Link error: ".$_SERVER['REQUEST_URI']."<br>";
            $msg.="Referer: ".$_SERVER['HTTP_REFERER']."<br>";
            $msg.="Parent Class: FImages<br>";
            $msg.=$message;
            if (defined("SITE_CHARSET")) $CODE = SITE_CHARSET;
            else $CODE = 'utf-8';
            header("Content-Type: text/html; charset=".$CODE);
            echo $msg;
            exit;
        }
    }

    /**
     * Установка режима отладки
     * @param bool $debug - true/false
     */
    public function setDebug ($debug = false) {
        if (is_bool($debug)) $this->debug = $debug;
    }

    /**
     * Расчёт размеров ширины и высоты при создании обрезанного изображения
     */
    function getCutSize () {
        $s = $this->w/$this->h;
        $bs = $this->bw/$this->bh;
        if ($this->bw >= $this->w && $this->bh >= $this->h) {
            if ($s == 1) {
                if ($bs > 1) {
                    $this->dx = ($this->bw - $this->bh)/2;
                    $this->bw = $this->bh;
                }
                if ($bs < 1) {
                    $this->dy = ($this->bh - $this->bw)/2;
                    $this->bh = $this->bw;
                }
            }
            elseif ($s > 1) {
                if ($bs > 1) {
                    if ($s > $bs) {
                        $bhn = $this->bw/$s;
                        $this->dy = ($this->bh - $bhn)/2;
                        $this->bh = $bhn;
                    }
                    if ($s < $bs) {
                        $bwn = $this->bh*$s;
                        $this->dx = ($this->bw - $bwn)/2;
                        $this->bw = $bwn;
                    }
                }
                if ($bs < 1 || $bs == 1) {
                    $bhn = $this->bw/$s;
                    $this->dy = ($this->bh - $bhn)/2;
                    $this->bh = $bhn;
                }
            }
            else {
                if ($bs > 1 || $bs == 1) {
                    $bwn = $this->bh*$s;
                    $this->dx = ($this->bw - $bwn)/2;
                    $this->bw = $bwn;
                }
                if ($bs < 1) {
                    if ($s > $bs) {
                        $bhn = $this->bw/$s;
                        $this->dy = ($this->bh - $bhn)/2;
                        $this->bh = $bhn;
                    }
                    if ($s < $bs) {
                        $bwn = $this->bh*$s;
                        $this->dx = ($this->bw - $bwn)/2;
                        $this->bw = $bwn;
                    }
                }
            }
        }
        elseif ($this->bw >= $this->w && $this->bh <= $this->h) {
            $this->h = $this->bh;
            $this->dx = ($this->bw - $this->w)/2;
            $this->bw = $this->w;
        }
        elseif ($this->bw <= $this->w && $this->bh >= $this->h) {
            $this->w = $this->bw;
            $this->dy = ($this->bh - $this->h)/2;
            $this->bh = $this->h;
        }
        else {
            $this->w = $this->bw;
            $this->h = $this->bh;
        }
    }

    /**
     * Расчёт размеров ширины и высоты при создании изображения по заданным параметрам
     */
    private function getReSize () {
        $bs = $this->bw/$this->bh;
        if ($this->w >= $this->bw && $this->h >= $this->bh) {
            $this->w = $this->bw;
            $this->h = $this->bh;
        }
        elseif ($this->w >= $this->bw &&  $this->h < $this->bh) {
            $this->w = $this->h*$bs;
        }
        elseif ($this->w < $this->bw &&  $this->h >= $this->bh) {
            $this->h = $this->w/$bs;
        }
        else {
            $kh = $this->w/$bs;
            if ($kh <= $this->h) $this->h = $kh;
            else $this->w = $this->h*$bs;
        }
    }

    /**
     * Определение типа исходного изображения
     * @param $path - путь к изображению
     * @return int|string
     */
    public function getImageType ($path) {
        $type = 0;
        if (file_exists($path)) {
            list($width, $height, $type, $attr) = getimagesize($path);
            unset($width, $height, $attr);
            if ($type == 1) $type = 'GIF';
            elseif ($type == 2) $type = 'JPG';
            elseif ($type == 3) $type = 'PNG';
            elseif ($type == 4 || $type == 13) $type = 'SWF';
            else $type = 0;
        }
        return $type;
    }

    /**
     * Генерация и вывод капчи
     * @param bool $print - вывести в стандартный поток (true) или как src тэга img (false)
     * @return mixed
     */
    public function captcha($print = true){
        // https://github.com/alrusdi/spam.lan
        ########################## CAPTCHA configuration ###########################################
        $alphabet = "0123456789abcdefghijklmnopqrstuvwxyz"; # do not change without changing font files!
        # symbols used to draw CAPTCHA
        //$allowed_symbols = "0123456789"; #digits
        $allowed_symbols = (defined('CAPTCHA_SYMBOLS'))?CAPTCHA_SYMBOLS:$this->captcha_allowed_symbols; #alphabet without similar symbols (o=0, 1=l, i=j, t=f)
        $fontsdir = (defined('CAPTCHA_FONTS_DIR'))?CAPTCHA_FONTS_DIR:$this->captcha_fontsdir;
        $length = (defined('CAPTCHA_LENGTH'))?CAPTCHA_LENGTH:$this->captcha_length;
        $width = (defined('CAPTCHA_W'))?CAPTCHA_W:$this->captcha_width;
        $height = (defined('CAPTCHA_H'))?CAPTCHA_H:$this->captcha_height;
        $fluctuation_amplitude = (defined('CAPTCHA_AMPLITUDE'))?CAPTCHA_AMPLITUDE:$this->captcha_amplitude;
        $no_spaces = (defined('CAPTCHA_NO_SPACES'))?CAPTCHA_NO_SPACES:$this->captcha_no_spaces;
        $show_credits = (defined('CAPTCHA_TEXT_SHOW'))?CAPTCHA_TEXT_SHOW:$this->captcha_show_credits;
        $credits = (defined('CAPTCHA_TEXT'))?CAPTCHA_TEXT:$this->captcha_credits;
        # CAPTCHA image colors (RGB, 0-255)
        //$foreground_color = array(0, 0, 0);
        //$background_color = array(220, 230, 255);
        $foreground_color = array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
        $background_color = array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
        # JPEG quality of CAPTCHA image (bigger is better quality, but larger file size)
        $jpeg_quality = (defined('CAPTCHA_QUALITY'))?CAPTCHA_QUALITY:$this->captcha_jpeg_quality;
        ################################### END config #############################################

        $fonts=array();

        $included_files = get_included_files();
        foreach ($included_files as $filename) {
            if (preg_match("/FImages\.php$/ui", $filename)) {
                $dir_path = preg_replace("/FImages\.php$/ui", "", $filename);
                $fontsdir_absolute=$dir_path.$fontsdir;
            }
        }
        // $fontsdir_absolute=dirname(__FILE__).'/'.$fontsdir;
        if ($handle = opendir($fontsdir_absolute)) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match('/\.png$/i', $file)) {
                    $fonts[]=$fontsdir_absolute.'/'.$file;
                }
            }
            closedir($handle);
        }
        $alphabet_length=strlen($alphabet);

        do {
            // generating random keystring
            while(true){
                $this->key_string='';
                for($i=0;$i<$length;$i++){
                    $this->key_string.=$allowed_symbols[mt_rand(0,strlen($allowed_symbols)-1)];
                }
                if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $this->key_string)) break;
            }

            $font_file=$fonts[mt_rand(0, count($fonts)-1)];
            $font=imagecreatefrompng($font_file);
            imagealphablending($font, true);
            $fontfile_width=imagesx($font);
            $fontfile_height=imagesy($font)-1;
            $font_metrics=array();
            $symbol=0;
            $reading_symbol=false;

            // loading font
            for($i=0;$i<$fontfile_width && $symbol<$alphabet_length;$i++){
                $transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

                if(!$reading_symbol && !$transparent){
                    $font_metrics[$alphabet[$symbol]]=array('start'=>$i);
                    $reading_symbol=true;
                    continue;
                }

                if($reading_symbol && $transparent){
                    $font_metrics[$alphabet[$symbol]]['end']=$i;
                    $reading_symbol=false;
                    $symbol++;
                    continue;
                }
            }

            $img=imagecreatetruecolor($width, $height);
            imagealphablending($img, true);
            $white=imagecolorallocate($img, 255, 255, 255);
            //$black=imagecolorallocate($img, 0, 0, 0);

            imagefilledrectangle($img, 0, 0, $width-1, $height-1, $white);

            // draw text
            $x=1;
            for($i=0;$i<$length;$i++){
                $m=$font_metrics[$this->key_string[$i]];

                $y=mt_rand(-$fluctuation_amplitude, $fluctuation_amplitude)+($height-$fontfile_height)/2+2;

                if($no_spaces){
                    $shift=0;
                    if($i>0){
                        $shift=10000;
                        for($sy=7;$sy<$fontfile_height-20;$sy+=1){
                            for($sx=$m['start']-1;$sx<$m['end'];$sx+=1){
                                $rgb=imagecolorat($font, $sx, $sy);
                                $opacity=$rgb>>24;
                                if($opacity<127){
                                    $left=$sx-$m['start']+$x;
                                    $py=$sy+$y;
                                    if($py>$height) break;
                                    for($px=min($left,$width-1);$px>$left-12 && $px>=0;$px-=1){
                                        $color=imagecolorat($img, $px, $py) & 0xff;
                                        if($color+$opacity<190){
                                            if($shift>$left-$px){
                                                $shift=$left-$px;
                                            }
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        if($shift==10000){
                            $shift=mt_rand(4,6);
                        }

                    }
                }else{
                    $shift=1;
                }
                imagecopy($img, $font, $x-$shift, $y, $m['start'], 1, $m['end']-$m['start'], $fontfile_height);
                $x+=$m['end']-$m['start']-$shift;
            }
        }while($x>=$width-10); // while not fit in canvas

        $center=$x/2;

        // credits. To remove, see configuration file
        $img2=imagecreatetruecolor($width, $height+($show_credits?12:0));
        $foreground=imagecolorallocate($img2, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
        $background=imagecolorallocate($img2, $background_color[0], $background_color[1], $background_color[2]);
        imagefilledrectangle($img2, 0, 0, $width-1, $height-1, $background);
        imagefilledrectangle($img2, 0, $height, $width-1, $height+12, $foreground);
        $credits=empty($credits)?$_SERVER['HTTP_HOST']:$credits;
        //imagettftext($img2, 8, 0, $width/2-7*mb_strlen($credits)/2, $height+10, $white, './fonts/cour.ttf', $credits);
        imagettftext($img2, 8, 0, $width/2-7*mb_strlen($credits)/2, $height+10, $white, $fontsdir_absolute.'/cour.ttf', $credits);
        //imagestring($img2, 2, $width/2-imagefontwidth(2)*mb_strlen($credits)/2, $height-2, $credits, $background);

        // periods
        $rand1=mt_rand(750000,1200000)/10000000;
        $rand2=mt_rand(750000,1200000)/10000000;
        $rand3=mt_rand(750000,1200000)/10000000;
        $rand4=mt_rand(750000,1200000)/10000000;
        // phases
        $rand5=mt_rand(0,31415926)/10000000;
        $rand6=mt_rand(0,31415926)/10000000;
        $rand7=mt_rand(0,31415926)/10000000;
        $rand8=mt_rand(0,31415926)/10000000;
        // amplitudes
        $rand9=mt_rand(330,420)/110;
        $rand10=mt_rand(330,450)/110;

        //wave distortion

        for($x=0;$x<$width;$x++){
            for($y=0;$y<$height;$y++){
                $sx=$x+(sin($x*$rand1+$rand5)+sin($y*$rand3+$rand6))*$rand9-$width/2+$center+1;
                $sy=$y+(sin($x*$rand2+$rand7)+sin($y*$rand4+$rand8))*$rand10;

                if($sx<0 || $sy<0 || $sx>=$width-1 || $sy>=$height-1){
                    continue;
                }
                else{
                    $color=imagecolorat($img, $sx, $sy) & 0xFF;
                    $color_x=imagecolorat($img, $sx+1, $sy) & 0xFF;
                    $color_y=imagecolorat($img, $sx, $sy+1) & 0xFF;
                    $color_xy=imagecolorat($img, $sx+1, $sy+1) & 0xFF;
                }

                if($color==255 && $color_x==255 && $color_y==255 && $color_xy==255){
                    continue;
                }
                else if($color==0 && $color_x==0 && $color_y==0 && $color_xy==0){
                    $newred=$foreground_color[0];
                    $newgreen=$foreground_color[1];
                    $newblue=$foreground_color[2];
                }
                else{
                    $frsx=$sx-floor($sx);
                    $frsy=$sy-floor($sy);
                    $frsx1=1-$frsx;
                    $frsy1=1-$frsy;

                    $newcolor=(
                        $color*$frsx1*$frsy1+
                        $color_x*$frsx*$frsy1+
                        $color_y*$frsx1*$frsy+
                        $color_xy*$frsx*$frsy);

                    if($newcolor>255) $newcolor=255;
                    $newcolor=$newcolor/255;
                    $newcolor0=1-$newcolor;

                    $newred=$newcolor0*$foreground_color[0]+$newcolor*$background_color[0];
                    $newgreen=$newcolor0*$foreground_color[1]+$newcolor*$background_color[1];
                    $newblue=$newcolor0*$foreground_color[2]+$newcolor*$background_color[2];
                }

                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
            }
        }
        if ($print) {
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', FALSE);
            header('Pragma: no-cache');

            if (function_exists("imagepng")) {
                header("Content-Type: image/x-png");
                imagepng($img2);
            } else if (function_exists("imagegif")) {
                header("Content-Type: image/gif");
                imagegif($img2);
            } else if (function_exists("imagejpeg")) {
                header("Content-Type: image/jpeg");
                imagejpeg($img2, null, $jpeg_quality);
            }
            return true;
        }
        else {
            $tmpname = tempnam(TMP_DIR, $this->prefix); // 'MYIMG'
            if (file_exists($tmpname)) unlink($tmpname);
            $ext = "php";
            if (function_exists("imagepng")) {
                $tmpname = $tmpname.".".$ext;
                imagepng($img2, $tmpname);
            }
            else if (function_exists("imagegif")) {
                $ext = "gif";
                $tmpname = $tmpname.".".$ext;
                imagegif($img2, $tmpname);
            }
            else if (function_exists("imagejpeg")) {
                $ext = "jpeg";
                $tmpname = $tmpname.".".$ext;
                imagejpeg($img2, $tmpname, $jpeg_quality);
            }
            ImageDestroy($img2);
            $file = fopen($tmpname,"rb");
            $image = fread($file, filesize($tmpname));
            fclose($file);
            if (file_exists($tmpname)) unlink($tmpname);
            //if ($this->tmp_path) $this->deleteTmp();
            return "data:image/".$ext.";base64,".base64_encode($image);
        }
    }

    /**
     * Возврат символов капчи
     * @return mixed
     */
    public function getKeyString(){
        return $this->key_string;
    }

    /**
     * Возвращает логи
     * @return array
     */
    public function getLogs () {
        $return['log'] = $this->logs;
        $return['file'] = $this->log_file;
        return $return;
    }
}