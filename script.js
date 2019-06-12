function doValidate() {
    //alert("Last Text box was "+lastTextBox);
    var p = document.getElementById(lastTextBox);
            //var tagId = $(this).attr(id);
            //window.console && console.log('At JSON Dictionary. Tag id is ' + elementid);
            //var textinfo = document.getElementById(elementid).value;
    var textinfo = p.value;
    //window.console && console.log('info is '+textinfo);
    var len = textinfo.length;
    myresult = "blank";
    try {
      if( len > 2){
        //window.console && console.log('Ready for ajax ' + len);
        $.ajax({
               dataType: "json",
               url: 'jsonLanguage.php?ter'+'m='+textinfo,
               //data: data,
               success: function(data) {
                  //alert('Data: '+data.first);
                  myresult = data.first;
                  //myresult = "bad";
                  //window.console && console.log('Found result ' + myresult);
               },
               async: false
        });
        //alert("Language test is " + myresult);
        if (myresult == "bad") {
                  eid = '#'+lastTextBox;
                  $(eid).css("borderWidth", "2px");
                  $(eid).css("background-color", "bisque");
                  $(eid).css("border-color", "#980200");
                  $(eid).val(textinfo+" . . . Language filter was triggered.");
                  alert("Language filter for the last modified text box was triggered.");
                  return false;
        }
        $(eid).css("background-color", 'rgb(249, 255, 185)');
        $(eid).css("borderWidth", "2px");
        $(eid).css("border-color", "#886600");
      }
    } catch(e) {
        return false;
    }
    firstname = document.getElementById('fn').value;
    lastname = document.getElementById('ln').value;
    addr = document.getElementById('em').value;
    profession = document.getElementById('pf').value;
    goals = document.getElementById('gl').value;
    if (firstname == null || firstname == "" ||
        lastname == null || lastname == "" ||
        addr == null || addr == "" )
        // || profession == null || profession == ""
        // || goals == null || goals == ""
    {
        alert("Name and email are required.");
        return false;
    }
    if(!validateEmail(form1.email)){
                 return false;
    }
    return true;
}
function checkLanguage(elementid) {
    eid = '#'+lastTextBox;
    thisPhrase = document.getElementById(id=lastTextBox).value;
    //window.console && console.log(" Checking "+lastTextBox+" ... ");
    var len = thisPhrase.length;
    //window.console && console.log(" the length is "+len+" ... ");
    if( !(len > 2) ) {
        lastTextBox = elementid;
        return false;
    }
    //let promiseDict = new Promise(function(resolve, reject) {
        // executor (the producing code, "singer")
        checkJsonDictionary(lastTextBox).then(function(result){
           if (result == "bad" ) {
              window.console && console.log(" processing bad data ... ");
              $(eid).css("borderWidth", "2px");
              $(eid).css("background-color", "bisque");
              $(eid).css("border-color", "#980200");
              $(eid).val(thisPhrase + " ... language filter triggered. Please Review.");
              var longer = document.getElementById(id=lastTextBox).value;
              //window.console && console.log(" JavaScript retrieval is "+longer);
              lastTextBox = elementid;
              return false;
            }
            $(eid).css("background-color", 'rgb(249, 255, 185)');
            $(eid).css("borderWidth", "2px");
            $(eid).css("border-color", "#886600");
            lastTextBox = elementid;
            //window.console && console.log(" language filter passed test "+result);
            return true;
    });
}
// checkJsonDictionary is the nested function within checkLanguage()
// which handles the JSON request.
function checkJsonDictionary(elementid) {
    //console.log('Checking online JSON dictionary ...');
    //var def = $.Deferred();
    return new Promise(function(resolve, reject) {
        var p = document.getElementById(elementid);
        //var tagId = $(this).attr(id);
        //window.console && console.log('At JSON Dictionary. Tag id is ' + elementid);
        //var textinfo = document.getElementById(elementid).value;
        var textinfo = p.value;
        //window.console && console.log('info is '+textinfo);
        var len = textinfo.length;
        //window.console && console.log('The string length is ' + len);
        if( len > 2){
             $.getJSON('jsonLanguage.php?ter'+'m='+textinfo).then(function(data) {
                //window.console && console.log(textinfo+" is "+data.first);
                //return data.first;
                resolve(data.first);
             });
          } else {
              return "good";
          }
          //window.console && console.log(" Error occurred. " );
          //return false;
    });
}
function validateEmail(mail)
{
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail.value))
  {
    return (true);
  }
    alert("You have entered an invalid email address!");
    return (false);
}
function validateFirstName() {
        try {
          firstname = document.getElementById('fn').value;
          lastname = document.getElementById('ln').value;
          addr = document.getElementById('em').value;
          profession = document.getElementById('pf').value;
          goals = document.getElementById('gl').value;
          checkJsonDictionary('fn').then(function(data){
                //alert("Language variable equals " + data);
                eid = '#'+'fn';
                if (data == "bad" ) {
                  alert("Language filter for first name was triggered.");
                  $(eid).css("borderWidth", "10px");
                  $(eid).css("background-color", "bisque");
                  $(eid).css("border-color", "#986600");
                  return false;
                }
                return true;
            });
        } catch(e) {
            return false;
        }
        return false;
}
function validateLastName() {
        try {
          lastname = document.getElementById('ln').value;
          checkJsonDictionary('ln').then(function(data){
                eid = '#'+'ln';
                if (data == "bad" ) {
                  alert("Language filter for last name was triggered.");
                  $(eid).css("borderWidth", "10px");
                  $(eid).css("background-color", "bisque");
                  $(eid).css("border-color", "#986600");
                  return false;
                }
                return true;
            });
        } catch(e) {
            return false;
        }
        return false;
}
function removeSkill(cntSkill, removedCnt){
      //alert("skill count is " + cntSkill);
      removedCnt = removedCnt + 1;
      //alert("removed skill count is " + (removedCnt));
      //alert("skill removed, count decreases to " + (cntSkill - removedCnt));
      // Stored value of 'skillRemoved' is pre-incremented by one count.
      skillRemoved = removedCnt;
}
function removeEdu(cntEdu, removedCnt){
      removedCnt = removedCnt + 1;
      //alert("removed education count is " + removedCnt);
      //alert("An education has been removed, count decreases to " + (cntEdu - removedCnt));
      // Stored value of 'skillRemoved' is pre-incremented by one count.
      eduRemoved = removedCnt;
}
function removePosition(cntPosition, removedCnt){
      removedCnt = removedCnt + 1;
      //alert("removed position count is " + removedCnt);
      //alert("Position removed, count decreases to " + (cntPosition - removedCnt));
      positionRemoved = removedCnt;
}

// $.ajax({
//        dataType: "json",
//        url: 'jsonLanguage.php?ter'+'m='+textinfo,
//        //data: data,
//        success: function(data) {
//           //alert('Data: '+data.first);
//           alert('Data: '+data.first);
//           myresult = data.first;
//           //myresult = "bad";
//           window.console && console.log('Found result ' + myresult);
//        },
//        async: false
// });
