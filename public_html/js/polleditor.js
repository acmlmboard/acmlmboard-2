
function hex2(n)
{
	n = n.toString(16);
	if (n.length < 2) n = '0'+n;
	return n;
}

function addOption()
{
	var color = hex2(Math.floor(Math.random() * 255)) + hex2(Math.floor(Math.random() * 255)) + hex2(Math.floor(Math.random() * 255));
	
	var opt = document.createElement('div');
	opt.innerHTML = '<input type="text" name="opt[]" size=40 maxlength=40> - Color: <input class="color" name="col[]" value="' + color + '"> - <button class="submit" onclick="removeOption(this.parentNode);return false;">Remove</button>';
	
	document.getElementById('polloptions').appendChild(opt);
	jscolor.bind();
}

function removeOption(opt)
{
	opt.parentNode.removeChild(opt);
}
