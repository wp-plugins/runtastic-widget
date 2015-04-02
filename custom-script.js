jQuery(function($){
    $(document).ajaxComplete(function(){
        $('.color-field').wpColorPicker();
        $("a#runtastic-advanced-options-show").click(function(){  
            $("a#runtastic-advanced-options-hide").show();
            $( ".runtastic-advanced-options" ).toggle();
            $(this).hide();
            return false;
        });
        $("a#runtastic-advanced-options-hide").click(function(){  
            $("a#runtastic-advanced-options-show").show();
            $( ".runtastic-advanced-options" ).toggle();
            $(this).hide();
            return false;
        });
    });
    $(document).ready(function(){
        $('.color-field').wpColorPicker();
        $("a#runtastic-advanced-options-show").click(function(){  
            $("a#runtastic-advanced-options-hide").show();
            $( ".runtastic-advanced-options" ).toggle();
            $(this).hide();
            return false;
        });
        $("a#runtastic-advanced-options-hide").click(function(){  
            $("a#runtastic-advanced-options-show").show();
            $( ".runtastic-advanced-options" ).toggle();
            $(this).hide();
            return false;
        });
    });
});

