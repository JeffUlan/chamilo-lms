<?php

namespace Faker\Provider\zh_CN;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = array(
        '{{firstNameMale}}{{lastName}}',
    );

    protected static $femaleNameFormats = array(
        '{{firstNameFemale}}{{lastName}}',
    );

    protected static $firstNameMale = array(
        '任', '彭', '杨', '林', '毛', '胡', '陈', '雷', '龙',
        // below are unknown gender firstnames
        '丁', '万', '严', '于', '何', '余', '侯', '傅', '冯', '刘', '卢', '史', '叶',
        '吕', '吴', '周', '唐', '夏', '姚', '姜', '孔', '孙', '宋', '崔', '廖',
        '张', '徐', '方', '曹', '曾', '朱', '李', '杜', '梁', '武', '段', '江',
        '汪', '沈', '洪', '潘', '熊', '王', '田', '白', '秦', '程', '罗', '苏',
        '范', '莫', '萧', '董', '蒋', '蔡', '薛', '袁', '覃', '许', '谢', '谭',
        '贺', '贾', '赖', '赵', '邓', '邱', '邵', '邹', '郝', '郭', '金', '钟',
        '钱', '阎', '陆', '陶', '韦', '韩', '顾', '马', '高', '魏', '黄', '黎',
        '龚',
    );

    protected static $firstNameFemale = array(
        '孟', '尹', '戴', '石', '郑',
        // below are unknown gender firstnames
        '丁', '万', '严', '于', '何', '余', '侯', '傅', '冯', '刘', '卢', '史', '叶',
        '吕', '吴', '周', '唐', '夏', '姚', '姜', '孔', '孙', '宋', '崔', '廖',
        '张', '徐', '方', '曹', '曾', '朱', '李', '杜', '梁', '武', '段', '江',
        '汪', '沈', '洪', '潘', '熊', '王', '田', '白', '秦', '程', '罗', '苏',
        '范', '莫', '萧', '董', '蒋', '蔡', '薛', '袁', '覃', '许', '谢', '谭',
        '贺', '贾', '赖', '赵', '邓', '邱', '邵', '邹', '郝', '郭', '金', '钟',
        '钱', '阎', '陆', '陶', '韦', '韩', '顾', '马', '高', '魏', '黄', '黎',
        '龚',
    );

    protected static $lastName = array(
        '伟','芳','娜','敏','静','秀英','丽','强','磊','洋','艳','勇','军','杰','娟','涛','超','明','霞','秀兰','刚','平','燕','辉',
        '玲','桂英','丹','萍','鹏','华','红','玉兰','飞','桂兰','英','梅','鑫','波','斌','莉','宇','浩','凯','秀珍','健','俊','帆',
        '雪','帅','慧','旭','宁','婷','玉梅','龙','林','玉珍','凤英','晶','欢','玉英','颖','红梅','佳','倩','阳','建华','亮','成',
        '琴','兰英','畅','建','云','洁','峰','建国','建军','柳','淑珍','春梅','海燕','晨','冬梅','秀荣','瑞','桂珍','莹','秀云','桂荣',
        '志强','秀梅','丽娟','婷婷','玉华','兵','雷','东','琳','雪梅','淑兰','丽丽','玉','秀芳','欣','淑英','桂芳','博','丽华','丹丹',
        '彬','桂香','坤','想','淑华','荣','秀华','桂芝','岩','杨','小红','金凤','文','利','楠','红霞','建平','瑜','桂花','璐','凤兰'
    );
}
