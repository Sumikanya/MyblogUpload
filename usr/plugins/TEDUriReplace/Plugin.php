<?php
/**
 * 替换页面中的链接,使用七牛等
 *
 * @package TEDUriReplace
 * @author lgl
 * @version 1.0
 * @update:
 * @link http://www.typechodev.com
 */
class TEDUriReplace_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        #Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('TEDUriReplace_Plugin', 'url_replace');

        //在end这里处理,可以处理field中的url
        Typecho_Plugin::factory('index.php')->end = array('TEDUriReplace_Plugin', 'url_replace_and_echo');



    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){

        $rule_1 = new Typecho_Widget_Helper_Form_Element_Text('rule_1', null, 'replace_from=>replace_to', _t('规则一'), _t('替换规则:以"=>"分割'));
        $form->addInput($rule_1);

        $rule_2 = new Typecho_Widget_Helper_Form_Element_Text('rule_2', null, 'replace_from=>replace_to', _t('规则二'), _t('替换规则:以"=>"分割'));
        $form->addInput($rule_2);

        $rule_3 = new Typecho_Widget_Helper_Form_Element_Text('rule_3', null, 'replace_from=>replace_to', _t('规则三'), _t('替换规则:以"=>"分割'));
        $form->addInput($rule_3);

        $rule_4 = new Typecho_Widget_Helper_Form_Element_Text('rule_4', null, 'replace_from=>replace_to', _t('规则四'), _t('替换规则:以"=>"分割'));
        $form->addInput($rule_4);

        $rule_5 = new Typecho_Widget_Helper_Form_Element_Text('rule_5', null, 'replace_from=>replace_to', _t('规则五'), _t('替换规则:以"=>"分割'));
        $form->addInput($rule_5);

    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}




    public static function url_replace($content, $opt)
    {
        $config = Helper::options()->plugin('TEDUriReplace');

        $rules=array();
        $rules[] = "http://tianshimanbu.com=>https://tianshimanbu.com"; # 默认将站内http资源全部切换成https
        $rules[] = $config->rule_1;
        $rules[] = $config->rule_2;
        $rules[] = $config->rule_3;
        $rules[] = $config->rule_4;
        $rules[] = $config->rule_5;

        foreach ($rules as $rule){
            if(!empty($rule)){
                $tmp = explode('=>', $rule);
                if(count($tmp) != 2){
                    # 格式不正确,跳过
                    continue;
                }
                $from = trim($tmp[0]);
                $to = trim($tmp[1]);
                if($from == 'replace_from'){
                    # 默认字符串,跳过
                    continue;
                }
                $content = str_replace($from,$to,$content);
            }
        }
        return $content;
    }

    public static function url_replace_and_echo(){
        $output = ob_get_contents();
        ob_end_clean();

        $result = self::url_replace($output, null);
        echo $result;
    }

}