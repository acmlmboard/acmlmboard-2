"use strict";
var bState=new Array();

function bbCode(element, tag, tag2, userpref) {
    var id = document.getElementById(element);
    var selection = id.value.substring(id.selectionStart, id.selectionEnd);
    var cursorPosition = id.selectionEnd;
    var isThereNoSelection = (id.selectionEnd - id.selectionStart == 0);
    
    if (!tag2) {tag2 = tag;} // if there is no closing tag, make it the same as the opening tag
    
    if (tag === "url" && selection.substr(0, 4).toLowerCase() !== "http" && userpref !=0) { //Ignore this for Retro settings.
        var url = prompt("Enter the URL for the link:");
        if (url) {
            tag2 = "url";
            tag = "url=" + url;
        }
    }
    
    if (tag === "smilies") {
        
        var selectionBB = tag2;
        if (isThereNoSelection) {
            cursorPosition += tag2.length;
        } else {cursorPosition = id.selectionStart + tag2.length;}
        
    } else {
        if(userpref == 2) { // Modern Preference
          var selectionBB = "[" + tag + "]" + selection + "[/" + tag2 + "]";
          cursorPosition += tag.length + 2;
        } else { // Retro Preference
        if (isThereNoSelection) {
		if(bState[tag]==1) {
			var selectionBB = selection + "[/" + tag2 + "]";
			bState[tag]=0;
			document.getElementById(element).className="b n3";
			cursorPosition += (tag.length * 2) + 2
		} else {
			var selectionBB = "[" + tag + "]" + selection;
			bState[tag]=1;
			document.getElementById(element).className="b n1";
			cursorPosition += (tag.length * 2) + 3
		}
	} else {
		var selectionBB = "[" + tag + "]" + selection + "[/" + tag2 + "]";
		cursorPosition += (tag.length * 2) + 5
	}
        }
    }
    
    if (isThereNoSelection && id.selectionStart == 0 && id.length > 0) {
        id.selectionStart = id.length;
        id.selectionEnd = id.length;
    }
    
    var string1 = id.value.substring(0, id.selectionStart);
    var string2 = id.value.substring(id.selectionEnd);
    id.value = string1 + selectionBB + string2;
    
    id.focus({preventScroll: true});
    id.selectionEnd = cursorPosition;
    id.selectionStart = cursorPosition;
}
function togglesmiles()
{
          var table = document.getElementById('smilebar');
          var ishidden = 1;
          if(table.style['display'] == 'none') {
              table.style['display'] = '';
              ishidden = 0;
          }
          else {
            table.style['display'] = 'none';
            ishidden = 1;
          }
}
