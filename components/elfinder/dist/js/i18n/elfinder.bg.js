/**
 * Bulgarian translation
 * @author Stamo Petkov <stamo.petkov@gmail.com>
 * @version 2014-12-19, 2015-10-20
 */
(function(root, factory) {
	if (typeof define === 'function' && define.amd) {
		define(['elfinder'], factory);
	} else if (typeof exports !== 'undefined') {
		module.exports = factory(require('elfinder'));
	} else {
		factory(root.elFinder);
	}
}(this, function(elFinder) {
	elFinder.prototype.i18.bg = {
		translator : 'Stamo Petkov &lt;stamo.petkov@gmail.com&gt;',
		language   : 'Български',
		direction  : 'ltr',
		dateFormat : 'd M Y h:i A', // 13 Mar 2012 05:27 PM
		fancyDateFormat : '$1 h:i A', // will produce smth like: Today 12:25 PM
		messages   : {
			
			/********************************** errors **********************************/
			'error'                : 'Грешка',
			'errUnknown'           : 'Непозната грешка.',
			'errUnknownCmd'        : 'Непозната команда.',
			'errJqui'              : 'Грешна конфигурация на jQuery UI. Компонентите selectable, draggable и droppable трябва да са включени.',
			'errNode'              : 'elFinder изисква да бъде създаден DOM елемент.',
			'errURL'               : 'Грешка в настройките на elFinder! не е зададена стойност на URL.',
			'errAccess'            : 'Достъп отказан.',
			'errConnect'           : 'Няма връзка със сървъра.',
			'errAbort'             : 'Връзката е прекъсната.',
			'errTimeout'           : 'Просрочена връзка.',
			'errNotFound'          : 'Сървърът не е намерен.', 
			'errResponse'          : 'Грешен отговор от сървъра.',
			'errConf'              : 'Грешни настройки на сървъра.', 
			'errJSON'              : 'Не е инсталиран модул на PHP за JSON.',
			'errNoVolumes'         : 'Няма дялове достъпни за четене.',
			'errCmdParams'         : 'Грешни параметри на командата "$1".',
			'errDataNotJSON'       : 'Данните не са JSON.',
			'errDataEmpty'         : 'Липсват данни.',
			'errCmdReq'            : 'Запитването от сървъра изисква име на команда.',
			'errOpen'              : 'Не мога да отворя "$1".',
			'errNotFolder'         : 'Обектът не е папка.',
			'errNotFile'           : 'Обектът не е файл.',
			'errRead'              : 'Не мога да прочета "$1".',
			'errWrite'             : 'Не мога да пиша в "$1".',
			'errPerm'              : 'Разрешение отказано.',
			'errLocked'            : '"$1" е заключен и не може да бъде преименуван, местен или премахван.',
			'errExists'            : 'Вече съществува файл с име "$1"',
			'errInvName'           : 'Грешно име на файл.',
			'errFolderNotFound'    : 'Папката не е открита.',
			'errFileNotFound'      : 'Файлът не е открит.',
			'errTrgFolderNotFound' : 'Целевата папка "$1" не е намерена.',
			'errPopup'             : 'Браузъра блокира отварянето на прозорец. За да отворите файла, разрешете отварянето в настройките на браузъра.',
			'errMkdir'             : 'Не мога да създам папка"$1".',
			'errMkfile'            : 'Не мога да създам файл "$1".',
			'errRename'            : 'Не мога да преименувам "$1".',
			'errCopyFrom'          : 'Копирането на файлове от том "$1" не е разрешено.',
			'errCopyTo'            : 'Копирането на файлове в том "$1" не е разрешено.',
			'errMkOutLink'         : 'Не мога да създам връзка извън началото на ресурса.',
			'errUpload'            : 'Грешка при качване.',
			'errUploadFile'        : 'Не мога да кача "$1".',
			'errUploadNoFiles'     : 'Не са намерени файлове за качване.',
			'errUploadTotalSize'   : 'Данните превишават максимално допостумия размер.',
			'errUploadFileSize'    : 'Файла превишава максимално допустимия размер.',
			'errUploadMime'        : 'Не е позволен тип на файла.',
			'errUploadTransfer'    : '"$1" грешка при предаване.', 
			'errUploadTemp'        : 'Не мога да създам временен файл за качване.',
			'errNotReplace'        : 'Обект "$1" вече съществува на това място и не може да бъде заменен от обект от друг тип.',
			'errReplace'           : 'Не мога да заменя "$1".',
			'errSave'              : 'Не мога да запиша "$1".',
			'errCopy'              : 'Не мога да копирам "$1".',
			'errMove'              : 'Не мога да преместя "$1".',
			'errCopyInItself'      : 'Не мога да копирам "$1" върху самия него.',
			'errRm'                : 'Не мога да премахна "$1".',
			'errRmSrc'             : 'Не мога да премахна изходния файл(ове).',
			'errExtract'           : 'Не мога да извлеча файловете от "$1".',
			'errArchive'           : 'Не мога да създам архив.',
			'errArcType'           : 'Неподдържан тип на архива.',
			'errNoArchive'         : 'Файлът не е архив или е от неподдържан тип.',
			'errCmdNoSupport'      : 'Сървъра не поддържа тази команда.', 
			'errReplByChild'       : 'Папката “$1” не може да бъде заменена от съдържащ се в нея елемент.',
			'errArcSymlinks'       : 'От съображения за сигурност няма да бъдат разопаковани архиви съдържащи symlinks.',
			'errArcMaxSize'        : 'Архивните файлове превишават максимално допустимия размер.',
			'errResize'            : 'Не мога да преоразмеря "$1".',
			'errResizeDegree'      : 'Невалиден градус за ротация.',
			'errResizeRotate'      : 'Изображението не е ротирано.',
			'errResizeSize'        : 'Невалиден размер на изображение.',
			'errResizeNoChange'    : 'Размерът на изображението не е променен.',
			'errUsupportType'      : 'Неподдържан тип на файл.',
			'errNotUTF8Content'    : 'Файл "$1" не е в UTF-8 формат и не може да бъде редактиран.',
			'errNetMount'          : 'Не мога да монтирам "$1".',
			'errNetMountNoDriver'  : 'Неподдържан протокол.',
			'errNetMountFailed'    : 'Монтирането не е успешно.',
			'errNetMountHostReq'   : 'Хост се изисква.',
			'errSessionExpires'    : 'Сесията ви изтече поради липса на активност.',
			'errCreatingTempDir'   : 'Не мога да създам временна директория: "$1"',
			'errFtpDownloadFile'   : 'Не мога да изтегля файл от FTP: "$1"',
			'errFtpUploadFile'     : 'Не мога да кача файл на FTP: "$1"',
			'errFtpMkdir'          : 'Не мога да създам директория на FTP: "$1"',
			'errArchiveExec'       : 'Грешка при архивиране на файлове: "$1"',
			'errExtractExec'       : 'Грешка при разархивиране на файлове: "$1"',
			'errNetUnMount'        : 'Не мога да размонтирам',
			'errConvUTF8'          : 'Не е конвертируем до UTF-8',
			'errFolderUpload'      : 'Опитайте Google Chrome, ако искате да качите папка.',
			
			/******************************* commands names ********************************/
			'cmdarchive'   : 'Създай архив',
			'cmdback'      : 'Назад',
			'cmdcopy'      : 'Копирай',
			'cmdcut'       : 'Изрежи',
			'cmddownload'  : 'Свали',
			'cmdduplicate' : 'Дублирай',
			'cmdedit'      : 'Редактирай файл',
			'cmdextract'   : 'Извлечи файловете от архива',
			'cmdforward'   : 'Напред',
			'cmdgetfile'   : 'Избери файлове',
			'cmdhelp'      : 'За тази програма',
			'cmdhome'      : 'Начало',
			'cmdinfo'      : 'Информация',
			'cmdmkdir'     : 'Нова папка',
			'cmdmkfile'    : 'Нов текстови файл',
			'cmdopen'      : 'Отвори',
			'cmdpaste'     : 'Вмъкни',
			'cmdquicklook' : 'Преглед',
			'cmdreload'    : 'Презареди',
			'cmdrename'    : 'Преименувай',
			'cmdrm'        : 'Изтрий',
			'cmdsearch'    : 'Намери файлове',
			'cmdup'        : 'Една директория нагоре',
			'cmdupload'    : 'Качи файлове',
			'cmdview'      : 'Виж',
			'cmdresize'    : 'Размер на изображение',
			'cmdsort'      : 'Подреди',
			'cmdnetmount'  : 'Монтирай мрежов ресурс',
			'cmdnetunmount': 'Размонтирай',
			'cmdplaces'    : 'To Places', // added 28.12.2014
			'cmdchmod'     : 'Change mode', // from v2.1 added 20.6.2015
			
			/*********************************** buttons ***********************************/ 
			'btnClose'  : 'Затвори',
			'btnSave'   : 'Запиши',
			'btnRm'     : 'Премахни',
			'btnApply'  : 'Приложи',
			'btnCancel' : 'Отказ',
			'btnNo'     : 'Не',
			'btnYes'    : 'Да',
			'btnMount'  : 'Монтирай',
			'btnApprove': 'Отиди на $1 и одобри',
			'btnUnmount': 'Размонтирай',
			'btnConv'   : 'Конвертирай',
			'btnCwd'    : 'Тук',
			'btnVolume' : 'Ресурс',
			'btnAll'    : 'Всички',
			'btnMime'   : 'MIME тип',
			'btnFileName':'Име на файл',
			'btnSaveClose': 'Запази и затвори',
			
			/******************************** notifications ********************************/
			'ntfopen'     : 'Отваряне на папка',
			'ntffile'     : 'Отваряне на файл',
			'ntfreload'   : 'Презареждане съдържанието на папка',
			'ntfmkdir'    : 'Създавам директория',
			'ntfmkfile'   : 'Създавам файл',
			'ntfrm'       : 'Изтриване на файлове',
			'ntfcopy'     : 'Копиране на файлове',
			'ntfmove'     : 'Преместване на файлове',
			'ntfprepare'  : 'Подготовка за копиране на файлове',
			'ntfrename'   : 'Преименуване на файлове',
			'ntfupload'   : 'Качвам файлове',
			'ntfdownload' : 'Свалям файлове',
			'ntfsave'     : 'Запис на файлове',
			'ntfarchive'  : 'Създавам архив',
			'ntfextract'  : 'Извличам файловете от архив',
			'ntfsearch'   : 'Търся файлове',
			'ntfresize'   : 'Преоразмерявам изображения',
			'ntfsmth'     : 'Зает съм >_<',
			'ntfloadimg'  : 'Зареждам изображения',
			'ntfnetmount' : 'Монтирам мрежов ресурс',
			'ntfnetunmount': 'Размонтирам мрежов ресурс',
			'ntfdim'      : 'Извличам размерите на изображение',
			'ntfreaddir'  : 'Извличам информация за папка',
			'ntfurl'      : 'Взимам URL от връзка',
			'ntfchmod'    : 'Променям характеристики на файл',
			
			/************************************ dates **********************************/
			'dateUnknown' : 'неизвестна',
			'Today'       : 'Днес',
			'Yesterday'   : 'Вчера',
			'msJan'       : 'яну',
			'msFeb'       : 'фев',
			'msMar'       : 'мар',
			'msApr'       : 'апр',
			'msMay'       : 'май',
			'msJun'       : 'юни',
			'msJul'       : 'юли',
			'msAug'       : 'авг',
			'msSep'       : 'сеп',
			'msOct'       : 'окт',
			'msNov'       : 'ное',
			'msDec'       : 'дек',
			'January'     : 'януари',
			'February'    : 'февруари',
			'March'       : 'март',
			'April'       : 'април',
			'May'         : 'май',
			'June'        : 'юни',
			'July'        : 'юли',
			'August'      : 'август',
			'September'   : 'септември',
			'October'     : 'октомври',
			'November'    : 'ноември',
			'December'    : 'декември',
			'Sunday'      : 'неделя',
			'Monday'      : 'понеделник',
			'Tuesday'     : 'вторник',
			'Wednesday'   : 'сряда',
			'Thursday'    : 'четвъртък',
			'Friday'      : 'петък',
			'Saturday'    : 'събота',
			'Sun'         : 'нед', 
			'Mon'         : 'пон', 
			'Tue'         : 'вто', 
			'Wed'         : 'сря', 
			'Thu'         : 'чет', 
			'Fri'         : 'пет', 
			'Sat'         : 'съб',
			
			/******************************** sort variants ********************************/
			'sortname'          : 'по име', 
			'sortkind'          : 'по вид', 
			'sortsize'          : 'по размер',
			'sortdate'          : 'по дата',
			'sortFoldersFirst'  : 'Папките първи',
			
			/********************************** messages **********************************/
			'confirmReq'      : 'Изисква се подтвърждение',
			'confirmRm'       : 'Сигурни ли сте, че желаете да премахнете файловете?<br/>Това действие е необратимо!',
			'confirmRepl'     : 'Да заменя ли стария файл с новия?',
			'confirmConvUTF8' : 'Не е в UTF-8 формат<br/>Конвертиране до UTF-8?<br/>Съдържанието става в UTF-8 формат при запазване след конверсията.',
			'confirmNotSave'  : 'Има направени промени.<br/>Те ще бъдат загубени, ако не запишете промените.',
			'apllyAll'        : 'Приложи за всички',
			'name'            : 'Име',
			'size'            : 'Размер',
			'perms'           : 'Привилегии',
			'modify'          : 'Променен',
			'kind'            : 'Вид',
			'read'            : 'четене',
			'write'           : 'запис',
			'noaccess'        : 'без достъп',
			'and'             : 'и',
			'unknown'         : 'непознат',
			'selectall'       : 'Избери всички файлове',
			'selectfiles'     : 'Избери файл(ове)',
			'selectffile'     : 'Избери първият файл',
			'selectlfile'     : 'Избери последният файл',
			'viewlist'        : 'Изглед списък',
			'viewicons'       : 'Изглед икони',
			'places'          : 'Места',
			'calc'            : 'Изчисли', 
			'path'            : 'Път',
			'aliasfor'        : 'Връзка към',
			'locked'          : 'Заключен',
			'dim'             : 'Размери',
			'files'           : 'Файлове',
			'folders'         : 'Папки',
			'items'           : 'Елементи',
			'yes'             : 'да',
			'no'              : 'не',
			'link'            : 'Връзка',
			'searcresult'     : 'Резултати от търсенето',  
			'selected'        : 'Избрани елементи',
			'about'           : 'За',
			'shortcuts'       : 'Бързи клавиши',
			'help'            : 'Помощ',
			'webfm'           : 'Файлов менаджер за Интернет',
			'ver'             : 'Версия',
			'protocolver'        : 'версия на протокола',
			'homepage'        : 'Начало',
			'docs'            : 'Документация',
			'github'          : 'Разклонение в Github',
			'twitter'         : 'Последвайте ни в Twitter',
			'facebook'        : 'Присъединете се към нас във Facebook',
			'team'            : 'Екип',
			'chiefdev'        : 'Главен разработчик',
			'developer'       : 'разработчик',
			'contributor'     : 'сътрудник',
			'maintainer'      : 'поддръжка',
			'translator'      : 'преводач',
			'icons'           : 'Икони',
			'dontforget'      : 'и не забравяйте да си вземете кърпата',
			'shortcutsof'     : 'Преките пътища са изключени',
			'dropFiles'       : 'Пуснете файловете тук',
			'or'              : 'или',
			'selectForUpload' : 'Избери файлове за качване',
			'moveFiles'       : 'Премести файлове',
			'copyFiles'       : 'Копирай файлове',
			'rmFromPlaces'    : 'Премахни от Места',
			'aspectRatio'     : 'Отношение',
			'scale'           : 'Мащаб',
			'width'           : 'Ширина',
			'height'          : 'Височина',
			'resize'          : 'Преоразмери',
			'crop'            : 'Отрежи',
			'rotate'          : 'Ротирай',
			'rotate-cw'       : 'Ротирай 90 градуса CW',
			'rotate-ccw'      : 'Ротирай 90 градуса CCW',
			'degree'          : '°',
			'netMountDialogTitle' : 'Монтиране на мрежов ресурс',
			'protocol'        : 'Протокол',
			'host'            : 'Хост',
			'port'            : 'Порт',
			'user'            : 'Потребител',
			'pass'            : 'Парола',
			'confirmUnmount'  : 'Ще размонтирате $1?',
			'dropFilesBrowser': 'Пусни или вмъкни файлове от браузера',
			'dropPasteFiles'  : 'Пусни или вмъкни файлове тук',
			'encoding'        : 'Кодировка',
			'locale'          : 'Локали',
			'searchTarget'    : 'Цел: $1',
			'searchMime'      : 'Търсене по въведен MIME тип',
			'owner'           : 'Собственик',
			'group'           : 'Група',
			'other'           : 'Други',
			'execute'         : 'Изпълнява',
			'perm'            : 'Разрешение',
			'mode'            : 'Поведение',
			
			/********************************** mimetypes **********************************/
			'kindUnknown'     : 'Непознат',
			'kindFolder'      : 'Папка',
			'kindAlias'       : 'Връзка',
			'kindAliasBroken' : 'Счупена връзка',
			// applications
			'kindApp'         : 'Приложение',
			'kindPostscript'  : 'Postscript документ',
			'kindMsOffice'    : 'Microsoft Office документ',
			'kindMsWord'      : 'Microsoft Word документ',
			'kindMsExcel'     : 'Microsoft Excel документ',
			'kindMsPP'        : 'Microsoft Powerpoint презентация',
			'kindOO'          : 'Open Office документ',
			'kindAppFlash'    : 'Flash приложение',
			'kindPDF'         : 'PDF документ',
			'kindTorrent'     : 'Bittorrent файл',
			'kind7z'          : '7z архив',
			'kindTAR'         : 'TAR архив',
			'kindGZIP'        : 'GZIP архив',
			'kindBZIP'        : 'BZIP архив',
			'kindXZ'          : 'XZ архив',
			'kindZIP'         : 'ZIP архив',
			'kindRAR'         : 'RAR архив',
			'kindJAR'         : 'Java JAR файл',
			'kindTTF'         : 'True Type шрифт',
			'kindOTF'         : 'Open Type шрифт',
			'kindRPM'         : 'RPM пакет',
			// texts
			'kindText'        : 'Текстов документ',
			'kindTextPlain'   : 'Чист текст',
			'kindPHP'         : 'PHP изходен код',
			'kindCSS'         : 'CSS таблица със стилове',
			'kindHTML'        : 'HTML документ',
			'kindJS'          : 'Javascript изходен код',
			'kindRTF'         : 'RTF текстови файл',
			'kindC'           : 'C изходен код',
			'kindCHeader'     : 'C header изходен код',
			'kindCPP'         : 'C++ изходен код',
			'kindCPPHeader'   : 'C++ header изходен код',
			'kindShell'       : 'Unix shell script',
			'kindPython'      : 'Python изходен код',
			'kindJava'        : 'Java изходен код',
			'kindRuby'        : 'Ruby изходен код',
			'kindPerl'        : 'Perl изходен код',
			'kindSQL'         : 'SQL изходен код',
			'kindXML'         : 'XML документ',
			'kindAWK'         : 'AWK изходен код',
			'kindCSV'         : 'CSV стойности разделени със запетая',
			'kindDOCBOOK'     : 'Docbook XML документ',
			'kindMarkdown'    : 'Markdown текст',
			// images
			'kindImage'       : 'Изображение',
			'kindBMP'         : 'BMP изображение',
			'kindJPEG'        : 'JPEG изображение',
			'kindGIF'         : 'GIF изображение',
			'kindPNG'         : 'PNG изображение',
			'kindTIFF'        : 'TIFF изображение',
			'kindTGA'         : 'TGA изображение',
			'kindPSD'         : 'Adobe Photoshop изображение',
			'kindXBITMAP'     : 'X bitmap изображение',
			'kindPXM'         : 'Pixelmator изображение',
			// media
			'kindAudio'       : 'Аудио медия',
			'kindAudioMPEG'   : 'MPEG звук',
			'kindAudioMPEG4'  : 'MPEG-4 звук',
			'kindAudioMIDI'   : 'MIDI звук',
			'kindAudioOGG'    : 'Ogg Vorbis звук',
			'kindAudioWAV'    : 'WAV звук',
			'AudioPlaylist'   : 'MP3 списък за изпълнение',
			'kindVideo'       : 'Видео медия',
			'kindVideoDV'     : 'DV филм',
			'kindVideoMPEG'   : 'MPEG филм',
			'kindVideoMPEG4'  : 'MPEG-4 филм',
			'kindVideoAVI'    : 'AVI филм',
			'kindVideoMOV'    : 'Quick Time филм',
			'kindVideoWM'     : 'Windows Media филм',
			'kindVideoFlash'  : 'Flash филм',
			'kindVideoMKV'    : 'Matroska филм',
			'kindVideoOGG'    : 'Ogg филм'
		}
	};
}));

