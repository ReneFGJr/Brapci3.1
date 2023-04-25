<?php
echo form_open();
echo form_label(lang('brapci.Authority'),'idname',['class'=>'small']);
echo form_input('q',get('q'),['class'=>'form-control full border border-secondary']);
echo form_submit('action',lang('brapci.search'),['class'=>'btn btn-secondary mt-2']);
echo form_close();
?>