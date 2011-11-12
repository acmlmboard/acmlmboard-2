_Self = new function() {

    this.SearchTimeout = null;
    this.SearchText = "";
    this.Searcher = new XMLHttpRequest();

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

    //TODO Forum tag editing

    this.Init = function() {
        document.getElementById("srchnm").onkeyup = function() { _Self.SearchBoxChanged(this.value); };
        document.getElementById("btnadd").onclick = function() { _Self.AddLocalModerator(); }
        document.getElementById("btnrmv").onclick = function() { _Self.RequestRemoveLocalMod(); }
    };
    window.onload = this.Init;
}