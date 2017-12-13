$(document).ready(function() {
    AdjustHeight();
    $(window).resize(AdjustHeight);
    $('.pglrn').click(ListClick);

    if(window.scroll_to) $('#leftchild').scrollTop(window.scroll_to);
});




function AdjustHeight() {
    var new_height = $(window).height() - (64+57+25+22); // высота футера, поиска, шапки таблицы и 22
    $('#leftchild').css({ height: new_height + 'px' });
    $('#centerchild').css({ height: new_height + 'px' });
}

function ListClick() {
    var dept = this.id.substr(8) * 1;
    location.href = location.protocol + '//' + location.hostname + '/dept/view/' + dept;
}