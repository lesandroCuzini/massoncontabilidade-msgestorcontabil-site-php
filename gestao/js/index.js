function maskTelephone(textbox, blur) {

    var telephone = textbox.value.replace(/[^0-9]/g, '');

    if (/^\d{1,2}$/.test(telephone)) {
        telephone = '(' + telephone + ')';
    }
    if (/^\d{3,}$/.test(telephone)) {
        telephone = '(' + telephone.substring(0, 2) + ')' + telephone.substring(2);
    }
    if (/^.\d{2}.\d{5,8}$/.test(telephone)) {
        telephone = telephone.substring(0, 8) + '-' + telephone.substring(8);
    }
    if (/^.\d{2}.\d{9,}$/.test(telephone)) {
        telephone = telephone.substring(0, 9) + '-' + telephone.substring(9, 13);
    }
    var caretPos = getCursorPosition(textbox);
    var lastLength = textbox.value.length;
    textbox.value = telephone;
    var newLength = textbox.value.length;
    if (!blur) {
        setCursorPosition(textbox, caretPos + newLength - lastLength);
    }
}
function setCursorPosition(el, index) {
    if (el.createTextRange) {
        var range = el.createTextRange();
        range.move('character', index);
        range.select();
    } else if (el.selectionStart != null) {
        el.focus();
        el.setSelectionRange(index, index);
    }
}
function getCursorPosition(oField) {
    var iCaretPos = 0;
    // IE Support
    if (document.selection) {
        oField.focus();
        var oSel = document.selection.createRange();
        oSel.moveStart('character', -oField.value.length);
        iCaretPos = oSel.text.length;
    }
    // Firefox support
    else if (oField.selectionStart || oField.selectionStart == '0')
        iCaretPos = oField.selectionStart;
    return (iCaretPos);
}