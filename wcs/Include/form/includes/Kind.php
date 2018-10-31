<?php

/**
 *  Class for textarea controls
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @copyright  (c) 2006 - 2012 Stefan Gabos
 *  @package    Controls
 */
class Zebra_Form_Kind extends Zebra_Form_Control
{

    /**
     *  Adds an <textarea> control to the form.
     *
     *  <b>Do not instantiate this class directly! Use the {@link Zebra_Form::add() add()} method instead!</b>
     *
     *  <code>
     *  // create a new form
     *  $form = new Zebra_Form('my_form');
     *
     *  // add a textarea control to the form
     *  // the "&" symbol is there so that $obj will be a reference to the object in PHP 4
     *  // for PHP 5+ there is no need for it
     *  $obj = &$form->add('textarea', 'my_textarea');
     *
     *  // don't forget to always call this method before rendering the form
     *  if ($form->validate()) {
     *      // put code here
     *  }
     *
     *  // output the form using an automatically generated template
     *  $form->render();
     *  </code>
     *
     *  @param  string  $id             Unique name to identify the control in the form.
     *
     *                                  The control's <b>name</b> attribute will be the same as the <b>id</b> attribute!
     *
     *                                  This is the name to be used when referring to the control's value in the
     *                                  POST/GET superglobals, after the form is submitted.
     *
     *                                  This is also the name of the variable to be used in custom template files, in
     *                                  order to display the control.
     *
     *                                  <code>
     *                                  // in a template file, in order to print the generated HTML
     *                                  // for a control named "my_textarea", one would use:
     *                                  echo $my_textarea;
     *                                  </code>
     *
     *  @param  string  $default        (Optional) Default value of the textarea.
     *
     *  @param  array   $attributes     (Optional) An array of attributes valid for
     *                                  <b>{@link http://www.w3.org/TR/REC-html40/interact/forms.html#h-17.7 textarea}</b>
     *                                  controls (rows, cols, style, etc)
     *
     *                                  Must be specified as an associative array, in the form of <i>attribute => value</i>.
     *                                  <code>
     *                                  // setting the "rows" attribute
     *                                  $obj = &$form->add(
     *                                      'textarea',
     *                                      'my_textarea',
     *                                      '',
     *                                      array(
     *                                          'rows' => 10
     *                                      )
     *                                  );
     *                                  </code>
     *
     *                                  See {@link Zebra_Form_Control::set_attributes() set_attributes()} on how to set
     *                                  attributes, other than through the constructor.
     *
     *                                  The following attributes are automatically set when the control is created and
     *                                  should not be altered manually:<br>
     *
     *                                  <b>id</b>, <b>name</b>, <b>class</b>
     *
     *  @return void
     */
    function Zebra_Form_Kind($id, $default = '', $attributes = '')
    {
        // call the constructor of the parent class
        parent::Zebra_Form_Control();

        // set the private attributes of this control
        // these attributes are private for this control and are for internal use only
        // and will not be rendered by the _render_attributes() method
        $this->private_attributes =  array(

            'default_value',
            'disable_xss_filters',
            'locked',
            'type',
            'value',

        );

        // set the default attributes for the textarea control
        // put them in the order you'd like them rendered
        $this->set_attributes(

            array(

                'name'      =>  $id,
                'id'        =>  $id,
                'rows'      =>  5,
                'cols'      =>  80,           // used only for passing W3C validation
                'class'     =>  'control',
                'type'      =>  'textarea',
                'value'     =>  $default,
            )

        );

        // sets user specified attributes for the control
        $this->set_attributes($attributes);

    }

    /**
     *  Generates the control's HTML code.
     *
     *  <i>This method is automatically called by the {@link Zebra_Form::render() render()} method!</i>
     *
     *  @return string  The control's HTML code
     */
    function toHTML()
    {	// get private attributes
        $attributes = $this->get_attributes('value');
        $name = $this->attributes['name'];
        $theme = $this->attributes['theme'];
        if(C('SYS_DEFAULT_EDITOR') == 1){
            $theme = $theme ? $theme : 'normal';
            switch($theme){
                case 'simple' :
                    $theme = "[
                        'source','|','bold','italic','underline','formatblock','fontname','fontsize','forecolor','hilitecolor','insertorderedlist','insertunorderedlist','lineheight','justifyleft','justifycenter','justifyright','justifyfull','|','plainpaste','wordpaste','|','image','multiimage','insertvideo','insertfile','|','table','hr','pagebreak','link','removeformat','preview','|','fullscreen'
                    ]";
                    break;
                case 'normal' :
                case 'advanced' :
                    $theme = "[
                        'source','|','undo','redo','|','preview','template','code','cut','copy','paste','plainpaste','wordpaste','|','justifyleft','justifycenter','justifyright','justifyfull','insertorderedlist','insertunorderedlist','indent','outdent','subscript','superscript','clearhtml','quickformat','selectall','|','fullscreen','/',
                        'formatblock','fontname','fontsize','|','forecolor','hilitecolor','bold','italic','underline','strikethrough','lineheight','removeformat','|','image','multiimage','insertvideo','insertfile','table','hr','emoticons','baidumap','pagebreak','anchor','link','unlink','|','about'
                    ]";
                    break;
                case 'config' :
                    $theme = "[
                        'source','|','bold','italic','underline','fontname','fontsize','forecolor','hilitecolor','image','link','fullscreen'
                    ]";
                    break;
                default :
                    $theme = "[]";
                    break;
            }
            $soucre = '
            <script>
                KindEditor.ready(function(K) {
                    var editor_'.$name.' = K.create(\'textarea[name="'.$name.'"]\', {
                        uploadJson : "'.U('admin/Editor/Index').'",
                        fileManagerJson : "'.U('admin/Editor/Index').'",
                        allowFileManager : true,
                        filterMode : false,
                        items : '.$theme.',
                        minWidth : "310px",
                        afterCreate : function() {
                            var self = this;
                            K.ctrl(document, "s", function() {
                                self.sync();
                                K("form")[0].submit();
                            });
                            K.ctrl(self.edit.doc, "s", function() {
                                self.sync();
                                K("form")[0].submit();
                            });
                        }
                    });
                });
            </script>';
            return $soucre.'<textarea  ' . $this->_render_attributes() . '>' . (isset($attributes['value']) ? $attributes['value'] : '') . '</textarea>';
        }else{
            $theme = $theme ? $theme : 'normal';
            switch($theme){
                case 'simple' :
                    $theme = "[
                        ['fullscreen', 'source', '|', 'bold', 'italic', 'underline', '|', 'fontfamily', 'fontsize', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'lineheight', '|', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'insertimage', 'insertvideo', 'attachment', '|', 'inserttable', 'wordimage', '|', 'link', 'pagebreak']
                    ]";
                    break;
                case 'normal' :
                    $theme = "[
                        ['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight', '|', 'horizontal', 'pagebreak', '|', 'preview', 'selectall', 'cleardoc'],
                        ['paragraph', 'fontfamily', 'fontsize', '|', 'directionalityltr', 'directionalityrtl', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'link', 'unlink', 'anchor', '|', 'simpleupload', 'insertimage', 'insertvideo', 'music', 'attachment', '|', 'inserttable', 'snapscreen', 'wordimage', '|', 'searchreplace', 'drafts']
                    ]";
                    break;
                case 'advanced' :
                    $theme = "[
                        ['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight', '|', 'horizontal', 'pagebreak', '|', 'preview', 'selectall', 'cleardoc'],
                        ['paragraph', 'fontfamily', 'fontsize', '|', 'directionalityltr', 'directionalityrtl', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|', 'simpleupload', 'insertimage', 'insertvideo', 'music', 'attachment', 'emotion'],
                        ['inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'map', 'gmap', 'insertframe', 'spechars', 'date', 'snapscreen', 'wordimage', 'template', 'background', '|', 'insertcode', '|', 'blockquote', 'print', 'searchreplace', 'drafts', 'scrawl', 'help']
                    ]";
                    break;
                case 'config' :
                    $theme = "[
                        ['fullscreen', 'source', '|', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'indent', 'removeformat', 'pasteplain', '|', 'link', 'simpleupload']
                    ]";
                    break;
                default :
                    $theme = "[]";
                    break;
            }
            $soucre = '
            <script>
            $(function(){
                var width = $(".lm_title_l").width();
                if( width < 940 ) $("#hideleft").click();
                UE.getEditor("'.$name.'",{
                    serverUrl : "'.U('admin/Editor/Index').'",
                    toolbars  : '.$theme.',
                    wordCount : false,
                    elementPathEnabled : false,
                    UEDITOR_HOME_URL:"'.C("JL_CMSPATH").'wcs/Include/ueditor/",
                    shortcutMenu:["fontfamily", "fontsize", "bold", "italic", "underline", "forecolor", "backcolor", "insertorderedlist", "insertunorderedlist"]
                });
            });
            </script>';
            return '<script type="text/plain" ' . $this->_render_attributes() . '>' . (isset($attributes['value']) ? $attributes['value'] : '') . '</script>'.$soucre;
        }
    }
}
?>
