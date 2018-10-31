<?php

/**
 *  Class for button controls.
 *
 *  @author     Stefan Gabos <contact@stefangabos.ro>
 *  @copyright  (c) 2006 - 2012 Stefan Gabos
 *  @package    Controls
 */
class Zebra_Form_Wxld extends Zebra_Form_Control
{


    function Zebra_Form_Wxld($id, $caption, $attributes = '')
    {
    
        // call the constructor of the parent class
        parent::Zebra_Form_Control();
        
        // set the private attributes of this control
        // these attributes are private for this control and are for internal use only
        // and will not be rendered by the _render_attributes() method
        $this->private_attributes = array(

            'disable_xss_filters',
            'locked',

        );

        // set the default attributes for the button control
        // put them in the order you'd like them rendered
        $this->set_attributes(

            array(
                'type'  =>  'submit',
                'name'  =>  $id,
                'id'    =>  $id,
                'value' =>  $caption,
                'class' =>  'button',
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
    {
        $name  = $this->attributes['name'];
        $value  = $this->attributes['value'];
        $ld_nid  = $this->attributes['ld_nid'];
        $intro  = $this->attributes['intro'];
        $viewValue = $this->attributes['viewValue'];
        //$html  ='<input ' . $this->_render_attributes() . ($this->form_properties['doctype'] == 'xhtml' ? '/' : '') . '>';
        $html  ='<input type="hidden" id="wxldname" name="'.$name.'" value="'.$viewValue.'" ></input>';
        $html .='<script language="javascript" > var publicUrlInc="__INC__"; var dbpre="'.C('DB_PREFIX').'"; </script>
        		 <script language="javascript" src="__INC__/wxld/wxld.js"></script>
                 <script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script>';
        $html .= '<input type="hidden" id="wxld"  value="'.$value.'"/>';
        $html .= '<tr class="row even">
                <td valign="top"><label>'.$intro.':</label></td>
                <td valign="top"><div class="select_ld"></div></td>
                </tr>';
        $html .="<script>if($('#wxld').val()=='') getLD(dbpre+'wxld','id','fid','typename','id',$ld_nid);
        else  regetLD(dbpre+'wxld','id','fid','typename','id',$ld_nid);</script>";        
        return $html;

    }
    
}

?>