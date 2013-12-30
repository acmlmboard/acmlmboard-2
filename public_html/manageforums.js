// This file must be saved as UTF-8 or the localmod name search will break

function toggleAll(cls, enable)
{
	var elems = document.getElementsByClassName(cls);
	for (var i = 0; i < elems.length; i++)
		elems[i].disabled = !enable;
}

function ajaxGet(url, callback)
{
	var x = new XMLHttpRequest();
	x.onreadystatechange = function() 
	{
        if (this.readyState == 4 && this.status == 200) 
		{
            callback(this.responseText);
        }
    };
	x.open('GET', url);
	x.send(null);
}


function localmodSearch(field)
{
	var reslist = document.getElementById('addmod_list');
	
	var srch = field.value;
	if (srch.length < 3) return;
	
	ajaxGet('JSUserSearchByName.php?a=' + encodeURIComponent(srch), 
	function(res)
	{
		while (reslist.length > 0)
			reslist.remove(0);
		
		var lines = res.split('\n');
		for (var l = 0; l < lines.length; l++)
		{
			var line = lines[l].trim();
			if (line.length < 1) continue;
			
			var sep = line.lastIndexOf('¬');
			var username = line.substring(0,sep);
			var userid = line.substring(sep+1);
			
			var opt = document.createElement('option');
			opt.value = userid;
			opt.text = username;
			reslist.add(opt, null);
		}
	});
}

function chooseLocalmod(field)
{
	var text = field.options[field.selectedIndex].text;
	document.getElementById('addmod_name').value = text;
}

function addLocalmod()
{
	var field = document.getElementById('addmod_name');
	var user = field.value;
	ajaxGet('manageforums.php?ajax=localmodRow&user=' + encodeURIComponent(user), 
	function(res)
	{
		if (!res)
		{
			alert('Error: user \''+user+'\' could not be found.');
			return;
		}
		
		res = res.split('|');
		if (document.getElementById('localmod_'+res[0]))
		{
			alert('Error: user \''+user+'\' is already assigned to this forum.');
			return;
		}
		
		var row = document.createElement('div');
		row.innerHTML = res[1];
		
		document.getElementById('modlist').appendChild(row);
	});
}

function deleteLocalmod(elem)
{
	elem.parentNode.removeChild(elem);
}


var curTag = -1;

function newTag()
{
	if (curTag >= 0)
	{
		var curelem = document.getElementById('tag_'+curTag).parentNode.children[0];
		curelem.style.outline = 'none';
	}
	
	document.getElementById('tag_name').value = '';
	document.getElementById('tag_tag').value = '';
	document.getElementById('tag_color').color.fromString('808080');
	curTag = -1;
}

function editTag(bit)
{
	if (curTag >= 0)
	{
		var curelem = document.getElementById('tag_'+curTag).parentNode.children[0];
		curelem.style.outline = 'none';
	}
	
	var tag = document.getElementById('tag_'+bit).value;
	tag = tag.split('|');

	document.getElementById('tag_name').value = decodeURIComponent(tag[0]);
	document.getElementById('tag_tag').value = decodeURIComponent(tag[1]);
	document.getElementById('tag_color').color.fromString(decodeURIComponent(tag[2]));
	curTag = bit;
	
	curelem = document.getElementById('tag_'+bit).parentNode.children[0];
	curelem.style.outline = '1px solid white';
}

function deleteTag(bit,elem)
{
	if (bit == curTag)
		newTag();
	
	elem.parentNode.removeChild(elem);
}

function getFreeTagBit()
{
	for (var i = 0; i < 32; i++)
	{
		if (!document.getElementById('tag_'+i))
			return i;
	}
	
	return -1;
}

function saveTag(fid)
{
	var text = document.getElementById('tag_name').value;
	var tag = document.getElementById('tag_tag').value;
	var color = document.getElementById('tag_color').value;
	
	var isnew = false;
	if (curTag < 0)
	{
		isnew = true;
		curTag = getFreeTagBit();
		if (curTag < 0)
		{
			alert('Error: the maximum amount of thread tags for this forum has been reached.');
			return;
		}
	}
	
	ajaxGet('manageforums.php?ajax=tagRow&forum=' + fid + '&text=' + encodeURIComponent(text) + '&tag=' + encodeURIComponent(tag) + '&color=' + encodeURIComponent(color) + '&bit=' + curTag,
	function(res)
	{
		if (!res)
		{
			alert('Error: please enter a name, tag text and color for the tag.');
			return;
		}
		
		if (!isnew)
		{
			var row = document.getElementById('tag_'+curTag).parentNode;
			row.innerHTML = res;
			row.children[0].style.outline = '1px solid white';
		}
		else
		{
			var row = document.createElement('div');
			row.innerHTML = res;
			
			document.getElementById('taglist').appendChild(row);
			row.children[0].style.outline = '1px solid white';
		}
	});
}
