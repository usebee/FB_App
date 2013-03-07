$( document ).ready( function() {
    $(".defaultCheckBox").click(function() {
        window.location.href = this.src;
    });

    $(".delete").click(function() {
        if (confirm("You want to delete?")) {
            window.location.href = this.rel;
        }
    });

    $("#btnAddPage").click(function() {
        var isValidName = false;
        var isValidAlias = false;
        var isUnique = true;

        $("input.name").each(function (index){
             if($(this).val()){
                 isValidName = true;
             }
        });

        /*
        $("input.alias").each(function (index){
             if($(this).val()){
                 isValidAlias = true;
             }
        });
        */
        /*
        $("input.alias").each(function (index){
             if($(this).val()){
                 $.ajax({
                    url: this.src + '/' + $(this).val(),
                    success: function(json){
                        var result = jQuery.parseJSON(json);
                        if (parseInt(result.isUnique) === 0) {
                            alert(parseInt(result.isUnique));
                            isUnique = false;
                        }
                    }
		}); //end of $.ajax
             }
        });
        */

        if (!isValidName) {
            alert('Name is null');
        }

        /**
        if (!isValidAlias) {
            alert('Alias is null');
        }
        */
        //if (!isUnique) {
            //alert('Alias is exist. Please check alias.');
        //}


        if (isValidName) {
            //alert('SUBMIT');
            $("#frmAddPage").submit();
        }

    });


    $("#btnAddArticle").click(function() {
        var isValidTitle = false;

        $("input.title").each(function (index){
             if($(this).val()){
                 isValidTitle = true;
             }
        });

        if (!isValidTitle) {
            alert('Title is not null');
        }
        if (isValidTitle) {
            $("#frmAddArticle").submit();
        }

    });

    /* ARTICLE */
    $("select[name^='media_id']").change(function(){
        $("#article_preview_background").html("");
        $(this).children('option:selected').each(function(){
            $("#article_preview_background").append("<img src='"+media_image_path+$(this).text()+"' width=400 />");
        });
    });
    $("select[name^='media_id']").mouseover(function(e){
        var $target = $(e.target);
        if($target.is('option')){
            $("#article_preview_background").html("<img src='"+media_image_path+$target.text()+"' width=400 />");
        }
    });
    $("select[name^='media_id']").mouseout(function(e){
        $("#article_preview_background").html("");
        $(this).children('option:selected').each(function(){
            $("#article_preview_background").append("<img src='"+media_image_path+$(this).text()+"' width=400 />");
        });
    });

//    $("select[name^='media_id']").change();
});