<?php
/**
 * Part of cabbage-brother project.
 *
 * @copyright  Copyright (C) 2019 EricWu. All rights reserved.
 */

namespace App\Line\Flex;

use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpaceSize;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use Illuminate\Support\Facades\Log;

/**
 * The FlexEducation class.
 *
 * @since  {DEPLOY_VERSION}
 */
class FlexVegetable
{
    /**
     * Create sample restaurant flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get($query_result)
    {
        if (empty($query_result)) {
            return new TextMessageBuilder('抱歉，我找不到你想要的農作物');
        }
        $message_list = new MultiMessageBuilder();
        $bubble_list = array();
        $index = 0;
        foreach ($query_result as $vegetable){
            $bubble = BubbleContainerBuilder::builder()
                    //->setHero(self::createHeroBlock())
                    ->setBody(self::createBodyBlock($vegetable));
                    //->setFooter(self::createFooterBlock())
            array_push($bubble_list, $bubble);
            $index++;
            if ($index == 10) {
                $index = 0; 
                $carouselContainer = FlexMessageBuilder::builder()
                ->setAltText('農產品市價行情')
                ->setContents(
                    CarouselContainerBuilder::builder()
                        ->setContents($bubble_list)
                );
                $message_list->add($carouselContainer);
                $bubble_list = array();
            }
        }
        return $message_list;
        
    }


    private static function createBodyBlock($vegetable)
    {
        $info  = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::BASELINE)
                    ->setSpacing(ComponentSpacing::SM)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText($vegetable['作物名稱'].' ('.$vegetable['市場名稱'].')')
                            ->setWrap(true)
                            ->setSize(ComponentFontSize::LG)
                            ->setFlex(5)
                    ]),
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setMargin(ComponentMargin::LG)
                    ->setSpacing(ComponentSpacing::LG)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText('▲ '.$vegetable['上價'])
                            ->setColor('#ff6384')
                            ->setSize(ComponentFontSize::LG),
                        TextComponentBuilder::builder()
                            ->setText('－ '.$vegetable['平均價'])
                            ->setSize(ComponentFontSize::LG),
                        TextComponentBuilder::builder()
                            ->setText('▼ '.$vegetable['下價'])
                            ->setColor('#4bc0c0')
                            ->setSize(ComponentFontSize::LG),
                    ]),
                
            ]);
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents([$info]);
    }
}