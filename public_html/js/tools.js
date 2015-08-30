var bState = new Array();

function wrapSelection(e, prefix, suffix)
{
	var el = document.getElementById(e);
	if (document.selection) {
		//IE-like
		el.focus();
		document.selection.createRange().text = prefix + document.selection.createRange().text + suffix;
	} else if (typeof el.selectionStart != undefined) {
		//FF-like
		el.value = el.value.substring(0, el.selectionStart) + prefix + el.value.substring(el.selectionStart, el.selectionEnd) + suffix + el.value.substring(el.selectionEnd, el.value.length);
		el.focus();
	}
}

function selectionLength(e)
{
	var el = document.getElementById(e);
	if (document.selection)
		return document.selection.createRange().text.length;
	else if (typeof el.selectionStart != undefined) {
		return el.selectionEnd - el.selectionStart;
	}
}

function buttonProc(e, bk, leadin, leadout)
{
	if (selectionLength(e) > 0)
		wrapSelection(e, leadin, leadout);
	else {
		if (bState[bk] == 1) {
			wrapSelection(e, "", leadout);
			bState[bk] = 0;
			document.getElementById(bk).className = "b n3";
		} else {
			wrapSelection(e, leadin, "");
			bState[bk] = 1;
			document.getElementById(bk).className = "b n1";
		}
	}
}

function updatequickreplystatus(ishidden)
{
	x = new XMLHttpRequest();
	x.open('GET', 'userpref.php?field=hidequickreply&value=' + ishidden);
	x.send(null);
}

function togglequickreply()
{
	var table = document.getElementById('quickreply');
	var rows = table.getElementsByTagName('tr');
	var ishidden = 0;
	for (var i = 1; i < rows.length; i++)
	{
		if (rows[i].className == 'toolbar')
			continue;
		if (rows[i].style['display'] == 'none') {
			rows[i].style['display'] = '';
			ishidden = 0;
		}
		else {
			rows[i].style['display'] = 'none';
			ishidden = 1;
		}
	}
	updatequickreplystatus(ishidden);
}