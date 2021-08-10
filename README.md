# FImages
Класс работы с изображениями

# Содержание

- [Общие понятия](#общие-понятия)
- [Возможности класса FImages](#Возможности-класса-FImages)
- [Примеры](#Примеры)
    - [Основные параметры класса](#Основные-параметры-класса)
    - [Использование](#Использование)

# Общие понятия

Класс FImages предназначен для редактирования изображений и генерации капчи.
Для работы необходимо наличие PHP версии 5 и выше, а также библиотека GD.

# Возможности класса FImages

Класс FImages позволяет редактировать существующие и передаваемые изображения.

Позволяет изменять размер изображения до заданных параметров как пропорционально, так и с обрезкой изображения.

Создавать файл с заливкой исходным изображением, с растягиванием под заданное соотношение сторон.

Добавлять надпись на изображение.

Поворачивать изображение вокруг своей оси, а также отображать его зеркально.

Генерировать и выводить на экран капчу и многое другое.

# Примеры

## Основные параметры класса
```php
$TYPE - create as
     $TYPE = 'GIF';
     $TYPE = 'JPEG'; //$TYPE = 'JPG';
     $TYPE = 'PNG';
     $TYPE = 'WEBP';

$path    - Path to image;
$dpath   - Path to save;
$name    - new image name;
$w       - end width;
$h       - end height;
$img     - string of image
$save    - end results (куда и как сохранить/вывести файл)
     $save = 1 // replace old image and return this file as string
     $save = 2 // print new image
     $save = 3 // return new file as string
     $save != 1 or 2 or 3 //return descriptor of new file;
```
## Использование
Файл с обрезкой размера под заданные параметры
```php
include ("FImages.php");
$IMG = new FImages($TYPE, $w, $h);
$IMG->cut($path, $save);
```
Файл с обрезкой размера под заданные параметры и возврат его как строки
```php
include ("FImages.php");
$IMG = new FImages($TYPE, $w, $h);
$image = $IMG->cut($path);
```
Файл с растягиванием под заданное соотношение сторон
```php
include ("FImages.php");
$IMG = new FImages();
$IMG->resize($path, $save, $w, $h);
```
файл с пропорциональным изменением размера
```php
include ("FImages.php");
$IMG = new FImages();
$IMG->resize_pro($path, $save, $w, $h); # proportion resize
```
Файл с заливкой исходным изображением
```php
$IMG->fill($path, $save, $w, $h); # fill background
```
Создание временного файла из исходника и получение данных о нём
```php
$IMG->getTempFile($img, $function, $save, $w, $h);
```
Создание копии изображения
```php
include ("FImages.php");
$IMG = new FImages('JPG', 40, 40);
$ds = $IMG->resize($path);
$IMG->createImgCopy($ds, $dpath, $name);
```
Генерация капчи
```php
<img src='".$IMG->captcha(false)."'> or $IMG->captcha();
```
Возврат символов капчи для проверки
```php
$IMG->getKeyString(); // captcha in string
```