<?php

use Vinlon\Laravel\LayAdmin\SideBar;
use Vinlon\Laravel\LayAdmin\SideBarCollection;

$sidebars = new SideBarCollection();

/* 在此处定义应用菜单 */
$sidebars->add(
    SideBar::create('axie_origin', 'AxieOrigin')->iconClass('layui-icon-app')
        ->add(SideBar::create('axie_origin.price_monitor', '价格监控')->jumpTo('axie/price_monitor'))
        ->add(SideBar::create('axie_origin.auto_purchase', '自动购买')->jumpTo('axie/auto_purchase'))
        ->add(SideBar::create('axie_origin.tokens', 'Rune&Charm')->jumpTo('axie/tokens'))
        ->add(SideBar::create('axie_origin.axie_sales', 'Axie成交记录')->jumpTo('axie/axie_sales'))
        ->add(SideBar::create('axie_origin.leaderboard', '排行榜')->jumpTo('axie/leaderboard'))
        ->add(SideBar::create('axie_origin.battle_history', '战斗记录')->jumpTo('axie/battle_history'))
);
$sidebars->add(
    SideBar::create('axie_land', 'AxieLand')->iconClass('layui-icon-app')
        ->add(SideBar::create('axie_origin.query_monitor', '价格监控')->jumpTo('land/price_monitor'))
);

// 默认菜单
$sidebars->add(
    SideBar::create('_user', '系统管理员')->iconClass('layui-icon-key')->jumpTo('_base/user/user/')
);
$sidebars->add(
    SideBar::create('_my', '我的')->iconClass('layui-icon-username')
        ->add(SideBar::create('_my.profile', '基本资料')->jumpTo('_base/user/user/info'))
        ->add(SideBar::create('_my.password', '修改密码')->jumpTo('_base/user/user/password'))
);


return [
    /*
     * Admin页面Route Prefix
     * 默认值： admin, 此时管理页面访问地址为 {{APP_URL}}/admin
     */
    'route_prefix' => env('LAY_ADMIN_ROUTE_PREFIX', 'admin'),

    /*
     * Admin后台显示名称
     */
    'display_name' => env('LAY_ADMIN_DISPLAY_NAME', '后台管理系统'),

    /*
     * 自定义middleware
     */
    'middleware' => [
    ],

    /*
     * 菜单定义
     */
    'sidebars' => $sidebars->toArray(),

    /*
     * 角色定义类
     */
    'role_class' => \App\AdminRoleExtend::class,
];
