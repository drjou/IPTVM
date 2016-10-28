<?php
namespace app\models;
/**
 * 定义常量
 * 错误为ERR，消息为INFO
 */
class API{
    /**
     * Error List
     */
    const ERR0001 = [
        'id' => 'ERR0001',
        'content' => 'Need an accountId parameter in url.',
    ];
    
    const ERR0002 = [
        'id' => 'ERR0002',
        'content' => 'STB state is wrong, please contact with supplier.',
    ];
    
    const ERR0003 = [
        'id' => 'ERR0003',
        'content' => 'Need a productId parameter in url.',
    ];
    
    const ERR0004 = [
        'id' => 'ERR0004',
        'content' => 'You havn\'t bought this product, please buy it first.',
    ];
    
    const ERR0005 = [
        'id' => 'ERR0005',
        'content' => 'Need a directoryId parameter in url.',
    ];
    
    const ERR0006 = [
        'id' => 'ERR0006',
        'content' => 'There is no this directory.',
    ];
    
    const ERR0007 = [
        'id' => 'ERR0007',
        'content' => 'Need a channelId parameter in url.',
    ];
    
    const ERR0008 = [
        'id' => 'ERR0008',
        'content' => 'There is no this channel.',
    ];
    
    const ERR0009 = [
        'id' => 'ERR0009',
        'content' => 'Need an cardNumber parameter in url.',
    ];
    
    const ERR0010 = [
        'id' => 'ERR0010',
        'content' => 'Your card number is wrong! Please try again or contact with the supplier.',
    ];
    
    const ERR0011 = [
        'id' => 'ERR0011',
        'content' => 'Your card has been used. If you don\'t, please contact with the supplier.',
    ];
    
    /**
     * Info List 
     */
    const INFO0001 = [
        'id' => 'INFO0001',
        'content' => 'Sorry, your STB is not in service. You can obtain a video service by a product rechargeable card.'
    ];
    
    const INFO0002 = [
        'id' => 'INFO0002',
        'content' => 'Your stb has been disabled, please contact with the supplier for more infomation.'
    ];
    
    const INFO0003 = [
        'id' => 'INFO0003',
        'content' => 'You are a new customer with a video service package. You can activate it.'
    ];
    
    const INFO0004 = [
        'id' => 'INFO0004',
        'content' => 'You are a new customer. You can obtain a video service by a product rechargeable card.'
    ];
    
    const INFO0005 = [
        'id' => 'INFO0005',
        'content' => 'Your stb has been activated, there is no need to activate it again.'
    ];
    
    const INFO0006 = [
        'id' => 'INFO0006',
        'content' => 'Your stb can\'t be activated. You can obtain a video service by a product rechargeable card.'
    ];
    
    const INFO0007 = [
        'id' => 'INFO0007',
        'content' => 'Sorry,activate failed, please try again or contact with the supplier.'
    ];
    
    const INFO0008 = [
        'id' => 'INFO0008',
        'content' => 'Activate suuccessfully. Congratulations!'
    ];
    
    const INFO0009 = [
        'id' => 'INFO0009',
        'content' => 'Sorry, purchase failed. You are not allowed to purchase product, please activate your stb first.'
    ];
    
    const INFO0010 = [
        'id' => 'INFO0010',
        'content' => 'Purchase successfully. Congratulations!'
    ];
    
    const INFO0011 = [
        'id' => 'INFO0011',
        'content' => 'Sorry, purchase failed. Please try again or contact with the supplier.'
    ];
}