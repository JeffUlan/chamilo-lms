<?php

namespace Faker\Provider\sr_RS;

class Address extends \Faker\Provider\Address
{
    protected static $postcode = array('#####');

    protected static $streetPrefix = array(
        'Булевар',
    );

    protected static $street = array(
        'Краља Милана', 'Цара Душана', 'Николе Тесле', 'Михајла Пупина', 'Николе Пашића',
    );

    protected static $streetNameFormats = array(
        '{{street}}',
        '{{streetPrefix}} {{street}}',
    );

    protected static $streetAddressFormats = array(
        '{{streetName}} {{buildingNumber}}',
    );

    protected static $cityFormats = array(
        '{{cityName}}',
    );

    /**
     * @link http://sr.wikipedia.org/wiki/%D0%93%D1%80%D0%B0%D0%B4%D0%BE%D0%B2%D0%B8_%D1%83_%D0%A1%D1%80%D0%B1%D0%B8%D1%98%D0%B8
     */
    protected static $cityNames = array(
        'Београд', 'Ваљево', 'Врање', 'Зајечар', 'Зрењанин', 'Јагодина', 'Крагујевац', 'Краљево', 'Крушевац', 'Лесковац', 'Лозница', 'Ниш', 'Нови Пазар', 'Нови Сад', 'Панчево', 'Пожаревац', 'Приштина', 'Смедерево', 'Сомбор', 'Сремска Митровица', 'Суботица', 'Ужице', 'Чачак', 'Шабац',
    );

    /**
     * @link https://github.com/umpirsky/country-list/blob/master/country/cldr/sr_Cyrl/country.php
     */
    protected static $country = array(
        'Острво Асенсион', 'Андора', 'Уједињени Арапски Емирати', 'Авганистан', 'Антигве и Барбуда', 'Ангвила', 'Албанија', 'Арменија', 'Холандски Антили', 'Ангола', 'Антарктик', 'Аргентина', 'Америчка Самоа', 'Аустрија', 'Аустралија', 'Аруба', 'Аландска острва', 'Азербејџан', 'Босна и Херцеговина', 'Барбадос', 'Бангладеш', 'Белгија', 'Буркина Фасо', 'Бугарска', 'Бахреин', 'Бурунди', 'Бенин', 'Свети Бартоломеј', 'Бермуда', 'Брунеј', 'Боливија', 'Бразил', 'Бахами', 'Бутан', 'Буве Острва', 'Боцвана', 'Белорусија', 'Белизе', 'Канада', 'Кокос (Келинг) Острва', 'Конго - Киншаса', 'Централно Афричка Република', 'Конго - Бразавил', 'Швајцарска', 'Обала Слоноваче', 'Кукова Острва', 'Чиле', 'Камерун', 'Кина', 'Колумбија', 'Острво Клипертон', 'Костарика', 'Србија и Црна Гора', 'Куба', 'Капе Верде', 'Божићна острва', 'Кипар', 'Чешка', 'Немачка', 'Дијего Гарсија', 'Џибути', 'Данска', 'Доминика', 'Доминиканска Република', 'Алжир', 'Сеута и Мелиља', 'Еквадор', 'Естонија', 'Египат', 'Западна Сахара', 'Еритреја', 'Шпанија', 'Етиопија', 'Европска Унија', 'Финска', 'Фиџи', 'Фолкландска Острва', 'Микронезија', 'Фарска Острва', 'Француска', 'Габон', 'Велика Британија', 'Гренада', 'Грузија', 'Француска Гвајана', 'Гурнси', 'Гана', 'Гибралтар', 'Гренланд', 'Гамбија', 'Гвинеја', 'Гваделупе', 'Екваторијална Гвинеја', 'Грчка', 'Јужна Џорџија и Јужна Сендвич Острва', 'Гватемала', 'Гуам', 'Гвинеја-Бисао', 'Гвајана', 'Хонг Конг С. А. Р. Кина', 'Херд и Мекдоналд Острва', 'Хондурас', 'Хрватска', 'Хаити', 'Мађарска', 'Канарска острва', 'Индонезија', 'Ирска', 'Израел', 'Острво Ман', 'Индија', 'Британска територија у Индијском океану', 'Ирак', 'Иран', 'Исланд', 'Италија', 'Џерси', 'Јамајка', 'Јордан', 'Јапан', 'Кенија', 'Киргизстан', 'Камбоџа', 'Кирибати', 'Коморска Острва', 'Сент Китс и Невис', 'Северна Кореја', 'Јужна Кореја', 'Кувајт', 'Кајманска Острва', 'Казахстан', 'Лаос', 'Либан', 'Сент Луција', 'Лихтенштајн', 'Шри Ланка', 'Либерија', 'Лесото', 'Литванија', 'Луксембург', 'Летонија', 'Либија', 'Мароко', 'Монако', 'Молдавија', 'Црна Гора', 'Сент Мартин', 'Мадагаскар', 'Маршалска Острва', 'Македонија', 'Мали', 'Мијанмар [Бурма]', 'Монголија', 'Макао С. А. Р. Кина', 'Северна Маријанска Острва', 'Мартиник', 'Мауританија', 'Монсерат', 'Малта', 'Маурицијус', 'Малдиви', 'Малави', 'Мексико', 'Малезија', 'Мозамбик', 'Намибија', 'Нова Каледонија', 'Нигер', 'Норфолк Острво', 'Нигерија', 'Никарагва', 'Холандија', 'Норвешка', 'Непал', 'Науру', 'Ниуе', 'Нови Зеланд', 'Оман', 'Панама', 'Перу', 'Француска Полинезија', 'Папуа Нова Гвинеја', 'Филипини', 'Пакистан', 'Пољска', 'Сен Пјер и Микелон', 'Питкерн', 'Порто Рико', 'Палестинске територије', 'Португал', 'Палау', 'Парагвај', 'Катар', 'Остала океанија', 'Реинион', 'Румунија', 'Србија', 'Русија', 'Руанда', 'Саудијска Арабија', 'Соломонска Острва', 'Сејшели', 'Судан', 'Шведска', 'Сингапур', 'Света Јелена', 'Словенија', 'Свалбард и Јанмајен Острва', 'Словачка', 'Сијера Леоне', 'Сан Марино', 'Сенегал', 'Сомалија', 'Суринам', 'Сао Томе и Принципе', 'Салвадор', 'Сирија', 'Свазиленд', 'Тристан да Куња', 'Туркс и Кајкос Острва', 'Чад', 'Француске Јужне Територије', 'Того', 'Тајланд', 'Таџикистан', 'Токелау', 'Источни Тимор', 'Туркменистан', 'Тунис', 'Тонга', 'Турска', 'Тринидад и Тобаго', 'Тувалу', 'Тајван', 'Танзанија', 'Украјина', 'Уганда', 'Мања удаљена острва САД', 'Сједињене Америчке Државе', 'Уругвај', 'Узбекистан', 'Ватикан', 'Сент Винсент и Гренадини', 'Венецуела', 'Британска Девичанска Острва', 'С.А.Д. Девичанска Острва', 'Вијетнам', 'Вануату', 'Валис и Футуна Острва', 'Самоа', 'Јемен', 'Мајоте', 'Јужноафричка Република', 'Замбија', 'Зимбабве',
    );

    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    public static function street()
    {
        return static::randomElement(static::$street);
    }

    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }
}
