_Self = new function() {

    this.SearchTimeout = null;
    this.SearchText = "";
    this.Searcher = new XMLHttpRequest();
    this.CurrentTagIndex = -1;
    this.NextTagId = 0;
    this.Tags = null;
    this.CurrentTag = null;
    this.TagData = Array();

    this.TAG_DESCRIPTION = 0;
    this.TAG_INLINE = 1;
    this.TAG_EXISTS = 2;
    this.TAG_ID = 3;
    this.TAG_INDB = 4;
    this.TAG_ACTION = 5;
    this.TAG_BIT = 6;
    this.TAG_COLOUR = 7;

    this.ACT_NOTHING = 0; //Ignore this entry when saving
    this.ACT_DELETE = 1;  //Notify the server to delete this tag when saving
    this.ACT_CREATE = 2;  //Notify the server to create this tag when saving
    this.ACT_UPDATE = 3;  //Notify the server to update this tag when saving

    this.NewTag = function() {
        return Array("", "", false, ++this.NextTagId, false, this.ACT_NOTHING, -1, "808080");
    }

    this.Searcher.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            _Self.SearchResults(this.responseText);
        }
    };

    this.SearchBoxChanged = function(NewText) {
        clearTimeout(this.SearchTimeout);
        if (NewText.length > 0) {
            this.SearchTimeout = setTimeout("_Self.SearchBoxTimeout();", 300);
            this.SearchText = NewText;
        } else {
            this.SearchResults(""); //Clear
        }
    };

    this.SearchBoxTimeout = function() {
        this.Searcher.open("GET", "./JSUserSearchByName.php?a=" + encodeURIComponent(this.SearchText), true);
        this.Searcher.send();
    };

    this.SearchResults = function(ResponseText) {
        var ResultsData = ResponseText.split("\n");
        var Selector = document.getElementById("slctnm");

        while (Selector.hasChildNodes())
            Selector.removeChild(Selector.firstChild);

        for (var x in ResultsData) {
            if (!ResultsData[x].length)
                continue;
            var Match = ResultsData[x].split("¬");
            var NewOption = document.createElement("OPTION");
            NewOption.text = Match[0];
            NewOption.value = Match[1];
            Selector.appendChild(NewOption);
        }
    };

    this.AddLocalModerator = function() {
        var Selector = document.getElementById("slctnm");
        var SelectedUser = Selector.options[Selector.selectedIndex];
        this.RemoveLocalModerator(SelectedUser.value);
        var NewOption = document.createElement("OPTION");
        NewOption.text = SelectedUser.text;
        NewOption.value = SelectedUser.value;
        document.getElementById("lmods").appendChild(NewOption);
        document.getElementById("localmods").value += SelectedUser.value + ",";
    };

    this.RequestRemoveLocalMod = function() {
        var Option = document.getElementById("lmods").options[document.getElementById("lmods").selectedIndex];
        if (Option)
            this.RemoveLocalModerator(Option.value);
    };

    this.RemoveLocalModerator = function(Id) {
        document.getElementById("localmods").value = document.getElementById("localmods").value.replace(Id + ",", "");
        var Selector = document.getElementById("lmods");
        for (var x = 0; x < Selector.length; x++) {
            if (Selector.options[x].value == Id) {
                Selector.remove(x);
            }
        }
    };

    this.SetActiveTag = function(Tag) {
        if (typeof Tag == "number" && Tag >= 0) {
            this.SetActiveTag(this.Tags.options[this.Tags.selectedIndex].value);
            this.CurrentTagIndex = this.Tags.selectedIndex;
            return;
        }
        if (!Tag || typeof Tag == "number" || Tag.length == 0) {
            this.ClearTag();
        } else {
            this.CurrentTag = eval(Tag);
            document.getElementById("tglong").value = this.CurrentTag[this.TAG_DESCRIPTION];
            document.getElementById("tgshrt").value = this.CurrentTag[this.TAG_INLINE];
            document.getElementById("tgcol").color.fromString(this.CurrentTag[this.TAG_COLOUR]);
        }
    };


    this.UpdateCurrentTag = function() {
        this.CurrentTag[this.TAG_INLINE] = document.getElementById("tgshrt").value.replace("\"", "\\\"");
        this.CurrentTag[this.TAG_DESCRIPTION] = document.getElementById("tglong").value.replace("\"", "\\\"");
        this.CurrentTag[this.TAG_COLOUR] = document.getElementById("tgcol").color.toString();
    };

    this.TagDataIndexFromTagId = function(TagId) {
        for (var x = 0; x < this.TagData.length; x++)
            if (this.TagData[x][this.TAG_ID] == TagId)
            return x;
    };

    this.ClearTag = function() {
        this.CurrentTagIndex = -1;
        this.CurrentTag = this.NewTag();
        document.getElementById("tgshrt").value = "";
        document.getElementById("tglong").value = "";
        document.getElementById("tgcol").color.fromString("808080");
    };

    this.SaveTag = function() {
        this.UpdateCurrentTag();

        if (this.CurrentTag[this.TAG_EXISTS]) {
            // Existing Tag - Update
            this.CurrentTag[this.TAG_ACTION] = this.CurrentTag[this.TAG_INDB] ? this.ACT_UPDATE : this.ACT_CREATE;

            var CurOption = this.Tags.options[this.CurrentTagIndex];
            CurOption.text = this.CurrentTag[this.TAG_DESCRIPTION] + " (" + this.CurrentTag[this.TAG_INLINE] + ")";
            CurOption.value = this.TagToString(this.CurrentTag);

            this.TagData[this.TagDataIndexFromTagId(this.CurrentTag[this.TAG_ID])] = this.CurrentTag;
        } else {
            // New Tag
            if (this.Tags.options.length == 32)
                return alert("Each forum is limited to 32 tags");

            this.CurrentTag[this.TAG_ACTION] = this.ACT_CREATE;
            this.CurrentTag[this.TAG_EXISTS] = true;
            this.TagData.push(this.CurrentTag);

            var NewOption = document.createElement("OPTION");
            NewOption.text = this.CurrentTag[this.TAG_DESCRIPTION] + " (" + this.CurrentTag[this.TAG_INLINE] + ")";
            NewOption.value = this.TagToString(this.CurrentTag);
            this.Tags.appendChild(NewOption);
        }
        this.ClearTag();
    };

    this.TagToString = function(Tag) {
        return "[\"" + Tag[this.TAG_DESCRIPTION] + "\", \"" + Tag[this.TAG_INLINE] + "\", " + Tag[this.TAG_EXISTS] + ", \"" + Tag[this.TAG_ID] + "\", " + Tag[this.TAG_INDB] + ", " + Tag[this.TAG_ACTION] + ", " + Tag[this.TAG_BIT] + ", \"" + Tag[this.TAG_COLOR] + "\"]";
    };

    this.DeleteTag = function() {
        if (this.Tags.selectedIndex == -1)
            return;
        var ClearFields = this.Tags.selectedIndex == this.CurrentTagIndex;
        var Tag = this.Tags.options[this.Tags.selectedIndex]
        this.Tags.removeChild(Tag);

        var TDIndex = this.TagDataIndexFromTagId(eval(Tag.value)[this.TAG_ID]);
        if (this.TagData[TDIndex][this.TAG_INDB]) {
            this.TagData[TDIndex][this.TAG_ACTION] = this.ACT_DELETE;
        } else {
            this.TagData[TDIndex][this.TAG_ACTION] = this.ACT_NOTHING;
        }

        if (ClearFields)
            this.ClearTag();
    };

    this.SaveForum = function() {
        var Changes = "";
        var nTags = 0;
        for (var x = 0; x < this.TagData.length; x++) {
            var Tag = this.TagData[x];
            switch (Tag[this.TAG_ACTION]) {
                case this.ACT_CREATE:
                    Changes += "a;" + Tag[this.TAG_INLINE] + ";" + Tag[this.TAG_DESCRIPTION] + ";" + Tag[this.TAG_COLOUR] + ";";
                    break;
                case this.ACT_DELETE:
                    Changes += "d;" + Tag[this.TAG_BIT] + ";";
                    break;
                case this.ACT_UPDATE:
                    Changes += "u;" + Tag[this.TAG_BIT] + ";" + Tag[this.TAG_INLINE] + ";" + Tag[this.TAG_DESCRIPTION] + ";" + Tag[this.TAG_COLOUR] + ";";
                    break;
            }
        }
        document.getElementById("tagops").value = Changes;
    }

    this.Init = function() {
        document.getElementById("srchnm").onkeyup = function() { _Self.SearchBoxChanged(this.value); };
        document.getElementById("btnadd").onclick = function() { _Self.AddLocalModerator(); }
        document.getElementById("btnrmv").onclick = function() { _Self.RequestRemoveLocalMod(); }
        document.getElementById("clrtg").onclick = function() { _Self.ClearTag(); }
        document.getElementById("deltg").onclick = function() { _Self.DeleteTag(); }
        document.getElementById("savtg").onclick = function() { _Self.SaveTag(); }
        document.getElementById("modtg").onclick = function() { _Self.SetActiveTag(_Self.Tags.selectedIndex); }
        document.getElementById("svefm").onclick = function() { _Self.SaveForum(); }



        _Self.Tags = document.getElementById("tglst");
        if (_Self.Tags.options.length > 0) {
            _Self.NextTagId = eval(_Self.Tags.options[_Self.Tags.options.length - 1].value)[_Self.TAG_ID]; //[Bit, Inline, Descriptive, IsNew, ClientId]
        } else {
            _Self.NextTagId = 0;
        }
        for (var x = 0; x < _Self.Tags.options.length; x++)
            _Self.TagData.push(eval(_Self.Tags.options[x].value));

        _Self.ClearTag();
    };

    window.onload = this.Init;
}