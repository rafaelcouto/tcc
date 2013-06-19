
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <title>TCC</title>
        
        <?php echo link_tag('global.css') ?>
        <?php echo script_tag('lib/jquery.js'); ?>
        
        <script type="text/javascript">
            var base_url = '<?php echo base_url() ?>';
            var assets_url = '<?php echo $this->config->item('assets_url') ?>';
        </script>
        
        <?php if (isset($this->css)) foreach ($this->css as $css)
                echo link_tag($css); ?>
        
        <?php if (isset($this->js)) foreach ($this->js as $js)
                echo script_tag($js); ?>
        
    </head>
    
    <body>
        
        <div id="general">
            <?php echo $content_for_layout ?>
        </div>

    </body>
    
</html>
