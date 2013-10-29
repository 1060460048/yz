<?php
/**
 * YzTextTransformBehavior class file.
 *
 * @author Pavel Agalecky <pavel.agalecky@gmail.com>
 */

/**
 * YzTextTransformBehavior is the behavior class that can be attached to any ActiveRecord model.
 * This behavior formats text from one of the supported formats into html.
 * Currently supported formats are:
 * <ul>
 * <li>Wiki - not supported yet</li>
 * <li>Html - uses {@see CHtmlPurifier} class to filter html</li>
 * <li>Markdown - uses built-in Yii's parser</li>
 * <li>BBCode - not supported yet</li>
 * </ul>
 *
 * @todo Add support for wiki and bbcode formats
 */
class YzTextTransformBehavior extends CActiveRecordBehavior
{
    const HTML_FORMAT = 'html';
    const MARKDOWN_FORMAT = 'markdown';
    const WIKI_FORMAT = 'wiki';
    const BBCODE_FORMAT = 'bbcode';

    /**
     * Text property of model
     * @var string
     */
    public $textProperty = 'text';
    /**
     * textHtml property of model
     * @var string
     */
    public $textHtmlProperty = 'text_html';
    /**
     * format property of model
     * @var string
     */
    public $formatProperty = 'format';
    /**
     * Whether use fixed format value instead of check property of the model
     * @var boolean|string
     */
    public $fixedFormat = false;
    /**
     * Options for HTML purifying. See {@see CHtmlPurifier} for more details.
     * @var array
     */
    public $htmlPurifyOptions = null;
    /**
     * Set this property to true, if you want to update your attribute either on beforeSave and
     * on afterFind events. Default is false
     * @var bool
     */
    public $processOnAfterFind = false;
    /**
     * If this property is set to true and {@see $processOnAfterFind} is true too, than if new
     * value of model's {@see $textHtmlProperty} is differ from the old one, it will be updated
     * in the database. This attribute is usefull if you want to update records in database after changing
     * settings in (for ex.) {@see $htmlPurifyOptions}, but don't want to run complete database rescan.
     *
     * Note: setting this property to true may cause longer page rendering.
     * @var bool
     */
    public $updateOnAfterFind = false;
    /**
     * Whether to allow special tags (ex. url)
     * @var bool
     */
    public $allowSpecialTags = true;
    /**
     * Active Record scenario
     * @var string|array
     */
    public $on = array();

    public function beforeSave($event)
    {
        if(!is_array($this->on))
            $this->on = preg_split('/[ ,]+/',$this->on);

        $this->processText();
    }

    public function afterFind($event)
    {
        if($this->processOnAfterFind) {
            $oldHtmlProperty = $this->owner->{$this->textHtmlProperty};
            $this->processText();
            if($this->updateOnAfterFind) {
                if(crc32($oldHtmlProperty) != crc32($this->owner->{$this->textHtmlProperty})) {
                    $this->owner->updateByPk($this->owner->getPrimaryKey(),array(
                        $this->textHtmlProperty => $this->owner->{$this->textHtmlProperty},
                        $this->textProperty => $this->owner->{$this->textProperty},
                    ));
                }
            }
        }
    }

    protected function processText()
    {
        $format = ($this->fixedFormat === false) ?
            $this->owner->{$this->formatProperty} : $this->fixedFormat;

        if(empty($this->on) || in_array($this->owner->scenario, $this->on)) {
            switch($format) {
                case self::HTML_FORMAT:
                    if($this->htmlPurifyOptions !== null) {
                        $p = new CHtmlPurifier();
                        $p->options = $this->htmlPurifyOptions;
                        $this->owner->{$this->textHtmlProperty} =
                            $p->purify($this->owner->{$this->textProperty});
                        $this->owner->{$this->textProperty} = $this->owner->{$this->textHtmlProperty};
                    } else {
                        $this->owner->{$this->textHtmlProperty} =
                            $this->owner->{$this->textProperty};
                    }
                    break;
                case self::MARKDOWN_FORMAT:
                    $markdown = new CMarkdownParser();
                    $this->owner->{$this->textHtmlProperty} =
                        $markdown->transform($this->owner->{$this->textProperty});
                    break;
                case self::WIKI_FORMAT:
                case self::BBCODE_FORMAT:
                default:
                    throw new CException(strtr('Format {format} not supported yet',array(
                        '{format}' => $format,
                    )));
            }
            if($this->allowSpecialTags)
                $this->owner->{$this->textHtmlProperty} =
                    $this->proceedSpecialTags($this->owner->{$this->textHtmlProperty});
        }
    }


    /**
     * @param string $html
     * @return string
     */
    protected function proceedSpecialTags($html)
    {
        // Base url
        $html = strtr($html, array(
            '{baseUrl}' => Yii::app()->baseUrl,
        ));
        return $html;
    }
}