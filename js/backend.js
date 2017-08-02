/* * * * * * Options page * * * * * */
jQuery(document).ready(function ($) {
    //carrousel
    $("#optionspage form h2").click(function(){
        $(".form-table").hide();
        if ($(this).hasClass( "checked" )){
            $(this).removeClass( "checked" );
        }else{
            $("form h2.checked").removeClass( "checked" );
            $(this).addClass( "checked" );
            $("form h2.checked + .form-table").show();
        }
    })

    //multi authors
    $("select#multi")
        .change(function(){
            //save metadata
            $("#authors").val($(this).val().join());
            draw_multiauthors();
        })
        .ready(function(){ draw_multiauthors(); });

    function draw_multiauthors(){
        var m_authors = '';
        $("select#multi").children(":selected").each(function() {
            cur_author = "<p data-back='"+ $(this).val()+"'><span>&times;</span>" + $(this).html() + "</p>";
            m_authors = m_authors + cur_author;
        });
        if (m_authors)
            $(".m_authors").html(m_authors);
        else
            $(".m_authors").html('');
    }

    $('.m_authors').on('click', 'p', function () {
        var ids = $("#authors").val().split(",");
        var back = $(this).attr("data-back");
        ids.splice(ids.indexOf(back), 1);
        $("#authors").val(ids.join());
        //unselect item
        $("select#multi").children(":selected").each(function() {
            if ($(this).val()== back) {
                $(this).removeAttr("selected");
                return;
            }
        });
        // redraw multi authors
        draw_multiauthors();
    });
});
