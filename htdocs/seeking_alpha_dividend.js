
    var gTestMode;
    $(function()
    {
        $(document).ready(function () {
            $('#TestMode').click(function () {

                if (this.checked = true) {
                    gTestMode = true;
                }

            });
        });
    });



//-----------------------------------------------------------------------------

document.getElementById('iD').onload = function () {
      
        var val = document.getElementById('iD').value;
        document.getElementById('demo').innerHTML = val; 
    };

//-----------------------------------------------------------------------------
function getValue(e) {

    document.getElementById("alphaTable").deleteRow(e.parentNode.parentNode.rowIndex);
    document.getElementById("alphaTable").insertRow(e.parentNode.parentNode.rowIndex);
}


//-----------------------------------------------------------------------------
function myDeleteFunction() {
    document.getElementById("alphaTable").deleteRow(0);
}
//-----------------------------------------------------------------------------
function deleteRow(e) {
    //document.getElementById("iD").value = e.parentNode.parentNode.rowIndex;
    document.getElementById("iD").value = "DELETE ROW " + e.parentNode.parentNode.rowIndex;
    document.getElementById("deletePost").value = e.parentNode.parentNode.rowIndex;
document.getElementById("alphaTable").insertRow(e.parentNode.parentNode.rowIndex); 
document.getElementById("alphaTable").deleteRow(e.parentNode.parentNode.rowIndex);

}

//-----------------------------------------------------------------------------
$("#alphaTable tr").click(function () {
    $(this).toggleClass("selected");
});
//-----------------------------------------------------------------------------


function hideRows() {
    document.getElementById("demo").style.visibility = "hidden";
}


//-----------------------------------------------------------------------------
function openForm() {
        document.getElementById("myForm").style.display = "block";
}


//-----------------------------------------------------------------------------
function closeForm() {
        document.getElementById("myForm").style.display = "none";
}


//-----------------------------------------------------------------------------
/* submit if elements of class=auto_submit_item in the form changes */
$(function () {
    $(".auto_submit_item").change(function () {
        $(this).parents("form").submit();
    });
});

//-----------------------------------------------------------------------------
function setFocusTableValues() {
    tbTableValues.focus()
} 

//-----------------------------------------------------------------------------

$(function () {
    //  changes mouse cursor when highlighting lower right of box
    $(document).ready().on('focus', 'textarea', function (e) {
        var a = $(this).offset().top + $(this).outerHeight() - 16,	//	top border of bottom-right-corner-box area
            b = $(this).offset().left + $(this).outerWidth() - 16;	//	left border of bottom-right-corner-box area
        $(this).css
            ({
                cursor: e.pageY > a && e.pageX > b ? 'nw-resize' : ''
            });
    })
    //  the following simple make the textbox "Auto-Expand" as it is typed in
    /* .on('input', 'textarea', function(e) {
         //  the following will help the text expand as typing takes place
         while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
               $(this).height($(this).height()+1);
         };
        });*/

    $(document).ready().on('copy', 'textarea', function (e) {
        //  the following will help the text expand as typing takes place
        while ($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
            $(this).height($(this).height() + 1);
        };
    });
    $(document).ready().on('keyup', 'textarea', function (e) {
        //  the following will help the text expand as typing takes place
        while ($(textArea2).outerHeight() < this.scrollHeight + parseFloat($(textArea2).css("borderTopWidth")) + parseFloat($(textArea2).css("borderBottomWidth"))) {
            $(textArea2).height($(textArea2).height() + 1);
        };
    });

    $(document).ready().on('input', 'textarea', function (e) {
        //  the following will help the text expand as typing takes place
        while ($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
            $(this).height($(this).height() + 1);
        };
    });
})



