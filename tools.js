var bState=new Array();

function wrapSelection(e,prefix,suffix)
{
	var el=document.getElementById(e);
	if(document.selection) {
		//IE-like
		el.focus();
		document.selection.createRange().text=prefix+document.selection.createRange().text+suffix;
	} else if(typeof el.selectionStart != undefined) {
		//FF-like
		el.value=el.value.substring(0,el.selectionStart)+prefix+el.value.substring(el.selectionStart,el.selectionEnd)+suffix+el.value.substring(el.selectionEnd,el.value.length);
		el.focus();
	}
}

function selectionLength(e)
{
	var el=document.getElementById(e);
	if(document.selection) return document.selection.createRange().text.length;
	else if(typeof el.selectionStart != undefined) { return el.selectionEnd-el.selectionStart; }
}

function buttonProc(e,bk,leadin,leadout)
{
	if(selectionLength(e)>0) wrapSelection(e,leadin,leadout);
	else {
		if(bState[bk]==1) {
			wrapSelection(e,"",leadout);
			bState[bk]=0;
			document.getElementById(bk).className="b n3";
		} else {
			wrapSelection(e,leadin,"");
			bState[bk]=1;
			document.getElementById(bk).className="b n1";
		}
	}
}

