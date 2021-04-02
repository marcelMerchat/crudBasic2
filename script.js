function getcube(){
var number=document.getElementById("number").value;
alert(number*number*number);
}
function adjustWindow(){
  var w = $( window ).width();
  //$("input").css("font-size", "1em");
  if ( w > 1100) {
      $("#main").css("left", "25%");
      $("#main").css("right", "25%");
      $("#main").each(function () {
          this.style.setProperty( "width", "50%");
          //this.style.setProperty( "width", "40%", "important" );
      });
      $("h1").css("font-size", "1.8rem");
      $(".button-submit").css("font-size", "1.3rem");
      $(".anchor-radio-button").css("font-size", "2rem");
  } else {
      $("#top_left").hide();
      $("#top_right").hide();
  }
  if (w > 900 && w < 1101) {
      $("#main").css("left", "20%");
      $("#main").css("right", "20%");
      $("#main").css("width", "60%");
      $("h1").css("font-size", "1.7rem");
      $(".button-submit").css("font-size", "1.3rem");
      $(".anchor-radio-button").css("font-size", "2rem");
  }
  if (w > 700 && w < 901) {
      $("#main").css("left", "10%");
      $("#main").css("right", "10%");
      $("#main").css("width", "80%");
      $("h1").css("font-size", "1.6rem");
      $(".button-submit").css("font-size", "1.3rem");
      $(".anchor-radio-button").css("font-size", "2rem");
  }
  if (w > 500 && w < 701) {
      $("#main").css("left", "5%");
      $("#main").css("right", "5%");
      $("#main").css("width", "90%");
      $("h1").css("font-size", "1.5rem");
      $(".button-submit").css("font-size", "1.3rem");
      $(".anchor-radio-button").css("font-size", "2rem");
  }
  if (w > 400 && w < 501) {
      window.console && console.log('Found small device; width = ' + w);
      $("h1").css("font-size", "1.4rem");
      $(".button-submit").css("font-size", "1.4rem");
      $(".anchor-radio-button").css("font-size", "2rem");
      $("#main").css({"left":"3%","right":"3%"});
      $("#main").each(function () {
               this.style.setProperty( "width", "94%", "important" );
      });
  }
  if (w < 401) {
      window.console && console.log('Found small device; width = ' + w);
      $("h1").css("font-size", "1.3rem");
      $(".button-submit").css("font-size", "1.3rem");
      $(".anchor-radio-button").css("font-size", "2rem");
      $("#main").css({"left":"3%","right":"3%"});
      $("#main").each(function () {
               this.style.setProperty( "width", "94%", "important" );
      });
  }
}
function adjustResumeWindow(){
  //showWidth("shoWidth");
  //$( "div" ).text( "The width for the " + tagId + " is " + w + "px." );
  //document.getElementById("main").style.width = "90%";
  //var ws = window.screen.width;
  //window.console && console.log('The screen width is = ' + ws);
  var w = $( window ).width();
  window.console && console.log('The window width is = ' + w);
  if ( w > 1100) {
      $("#main").css("left", "25%");
      $("#main").css("right", "25%");
      $("#main").each(function () {
          this.style.setProperty( "width", "50%");
          //this.style.setProperty( "width", "40%", "important" );
      });
      $("h1").css("font-size", "1.8rem");
      $(".button-submit").css("font-size", "1.8rem");
  } else {
      $("#top_left").hide();
      $("#top_right").hide();
  }
  if (w > 900 && w < 1101) {
      $("#main").css("left", "10%");
      $("#main").css("right", "10%");
      $("#main").css("width", "80%");
      $("h1").css("font-size", "1.7rem");
      $(".button-submit").css("font-size", "1.7rem");
  }
  if (w > 700 && w < 901) {
      $("#main").css("left", "5%");
      $("#main").css("right", "5%");
      $("#main").css("width", "90%");
      $("h1").css("font-size", "1.6rem");
      $(".button-submit").css("font-size", "1.6rem");
  }
  if (w > 500 && w < 701) {
      $("#main").css("left", "3%");
      $("#main").css("right", "3%");
      $("#main").css("width", "94%");
      $("h1").css("font-size", "1.5rem");
      $(".button-submit").css("font-size", "1.5rem");
  }
  if (w > 400 && w < 501) {
      window.console && console.log('Found small device; width = ' + w);
      $("h1").css("font-size", "1.4rem");
      $(".button-submit").css("font-size", "1.4rem");
      $(".justify").css("text-align" , "left");
      $("#main").css({"left":"2%","right":"2%"});
      $("#main").each(function () {
               this.style.setProperty( "width", "96%", "important" );
      });
  }
  if (w < 401) {
      window.console && console.log('Found small device; width = ' + w);
      $("h1").css("font-size", "1.3rem");
      $(".button-submit").css("font-size", "1.3rem");
      $(".justify").css("text-align" , "left");
      $("#main").css({"left":"3%","right":"3%"});
      $("#main").each(function () {
               this.style.setProperty( "width", "94%", "important" );
      });
  }
}
function doValidate() {
   first_name = document.getElementById('fn').value;
   last_name = document.getElementById('ln').value;
   addr = document.getElementById('em').value;
   profession = document.getElementById('pf').value;
   //goals = document.getElementById('gl').value;
   if (first_name == null || first_name == "" ||
         last_name == null ||  last_name == "" ||
             addr == null ||  addr == ""   )
   {
        triggerAlert("Name and email are still required. Please try again.", true);
        return false;
   }
   first_name = first_name.trim();
   last_name = last_name.trim();
   addr = addr.trim();
   if (first_name == null || first_name == "" ||
       last_name == null ||  last_name == "" ||
           addr == null ||  addr == ""   )
   {
        triggerAlert("Name and email are still required. Please try again.", true);
        return false;
   }
   if(!validateEmail(form1.email)){
        // Search with regular expression.
        // Offensive language check of parts at website.
        return false;
   }


    var j = 0;
    valid = true;
    //window.console && console.log('Education: ' + school_array);
    school_array.forEach(function() {
       var field1 = school_array[j];
       var field2 = award_array[j];
       j = j + 1;
       //window.console && console.log('ready for schools: ' + field1);
       valid = valid && validateListItem(field1,j,"educational institution",format="text");
       //window.console && console.log('Ready for educational awards: ' + field2);
       valid = valid && validateListItem(field2,j,"education award",format="text");
       //valid = valid && validateListItem(field4,j,"job start year");
      });

   // work history
   window.console && console.log('ready for work history: ');

       var j = 0;
       window.console && console.log('Jobs: ' + org_array);
       org_array.forEach(function() {
          var field1 = org_array[j];
          var field2 = position_title_array[j];
          var field3 = position_desc_array[j];
          var field4 = org_year_start_array[j];
          j = j + 1;
          window.console && console.log('ready for organizations: ' + field1);
          valid = valid && validateListItem(field1,j,"organization",format="text");
          window.console && console.log('Ready for job titles: ' + field2);
          valid = valid && validateListItem(field2,j,"job titles",format="text");
          //window.console && console.log('Ready for job descriptions: ' + field3);
          //valid = valid && validateListItem(field3,j,"job description",format="text");
          window.console && console.log('job start year: ' + field4);
          valid = valid && validateListItem(field4,j,"job start year",format="number");
       });
//       //   Job position activities
//       // len = activity_array.length;
//       // for(i=0; i < len; i
//       //      var j = i + 1;
//       //      var field = activity_array[i];
//       //      var p = document.getElementById(field);
//       //      if (p===null)
//       //      {
//       //         triggerAlert("An activity for a job position is incomplete.", true);
//       //         flagDataEntryBox(field);
//       //         return false;
//       //      }
//       //      var text_info = p.value;
//       //      if ( text_info == null || text_info == "") {
//       //             triggerAlert("An activity for a job position is incomplete.", true);
//       //             flagDataEntryBox(field);
//       //             return false;
//       //      }
//       //      var text_info = p.value.trim();
//       //      if ( text_info == null || text_info == "") {
//       //          triggerAlert("An activity for a job position, Activity-" + j +
//       //             "  is still incomplete.", true);
//       //          flagDataEntryBox(field);
//       //          return false;
//       //      }
//       //  } // end of for loop
   var j = 0;
   window.console && console.log('Job Activities ' + activity_array);
   activity_array.forEach(function() {
     var field1 = activity_array[j];
     j = j + 1;
     valid = valid && validateListItem(field1,j,"job activity",format="text");
   });
   window.console && console.log('Javascript is complete.');
   return valid;
}
function validateSkill() {
  //submitted = true;
  window.console && console.log('ready for skills: ');
  // skillnum = skill_array.length;
  // for(i=0; i < skillnum; i++){
  //    var j = i + 1;
  //    var field = skill_array[i];
  //    // e.g.; 'jobskill2'
  //    //alert(" At skill " + field);
  //    var obj = document.getElementById(field);
  //    if (obj===null)
  //    {
  //       //alert("A skill does not exist.", true);
  //       continue;
  //    }
  //    var text_info = obj.value;
  //    //var val = document.getElementById().value;
  //    //jobskill"+j
  //    if (text_info == "" || text_info == null)
  //    {
  //        //alert(" For skill "+ field + ", the info is null. ");
  //        triggerAlert("A Skill is incomplete.", true);
  //        flagDataEntryBox(skill_array[i]);
  //        return false;
  //        continue;
  //    }
  //    var trimmed = text_info.trim();
  //    if ( trimmed == null || trimmed == "") {
  //         triggerAlert("A Skill is still incomplete.", true);
  //         flagDataEntryBox(skill_array[i]);
  //         return false;
  //    }
  //    //alert(" For skill " + field + ", the info is "+ text_info);
  //    window.console && console.log('Skill: ' + field +
  //      ' has value ' + obj.value);
  // } // end of for loop
  var j = 0;
  valid = true;
  //return_value = true;
  //window.console && console.log('Education: ' + school_array);
  skill_array.forEach(function() {
     var field1 = skill_array[j];
     j = j + 1;
     valid = valid && validateListItem(field1,j,"skill",format="text");
  });
  window.console && console.log('Javascript is complete.');
  return valid;
}
function validateListItem(field,j,error_text_name,format="text"){
   var obj = document.getElementById(field);
   //window.console && console.log('null check: '+ typeof(obj));
   //window.console && console.log('in Iteration-'+j + " for " + field );
   //alert("Loop-"+j+" for "+ field);
   if (obj === 'undefined' || obj === null) {
     alert("Undefined condition detected for " + field +
         " in Loop-"+j);
     if(format == "text"){
        flagDataEntryBox(field);
     }
     if(format == "number"){
        flagNumberEntryBox(field);
     }
     return false;
   }
   var text_info = obj.value;
   // return_value != false &&
   if ( text_info == "" || text_info == null ) {
      window.console && console.log('Empty check in Iteration-'+j + " for " + field );
      flagDataEntryBox(field);
      triggerAlert("Uncompleted condition detected for " + field +
          " in Loop-"+j, true);
      //alert("Uncompleted condition detected for " + field +
        //  " in Loop-"+j)
      return false;
   }
   var trimmed = text_info.trim();
   if ( trimmed == "" || trimmed == null ) {
       window.console && console.log('Trimmed check in Iteration-'+j + " for " + field );
       flagDataEntryBox(field);
       return false;
   }
   text_name = error_text_name;
   window.console && console.log('The ' + field +
    ' has value ' + trimmed);
   return true;
}
function validateCertifications() {
    //submitted = true;
   num = certif_array.length;
   var j = 0;
   valid = true;
   return_value = true;
   //window.console && console.log('Certificates: ' + certif_array);
   //window.console && console.log('Links: ' + certif_lnk_array);
   certif_array.forEach(function() {
      var field1 = certif_array[j];
      var field2 = certif_school_array[j];
      var field3 = certif_edu_provider_array[j];
      var field4 = certif_lnk_array[j];
      j = j + 1;
      window.console && console.log('ready for certificates: ' + field1);
      valid = valid && validateListItem(field1,j,"certificate",format="text");
      window.console && console.log('Ready for educational institution: ' + field2);
      valid = valid && validateListItem(field2,j,"educational institution",format="text");
      window.console && console.log('Ready for educational internet provider: ' + field3);
      valid = valid && validateListItem(field3,j,"educational internet provider",format="text");
      window.console && console.log('Ready for certificate links: ' + field4);
      valid = valid && validateListItem(field4,j,"certificate link",format="text");
 });
 window.console && console.log('Javascript is complete.');
 return valid;
}
function doValidateProjects() {
    //submitted = true;
    window.console && console.log('Ready for project name: ');
    //projnum = proj_array.length;
    var j = 0;
    valid = true;
    //return_value = true;
    //window.console && console.log('Certificates: ' + certif_array);
    //window.console && console.log('Links: ' + certif_lnk_array);
    proj_array.forEach(function() {
       var field1 = proj_array[j];
       var field2 = proj_report_array[j];
       var field3 = github_array[j];
       j = j + 1;
       window.console && console.log('Ready for project names: ' + field1);
       valid = valid && validateListItem(field1,j,"project name",format="text");
       window.console && console.log('Ready for project reports: ' + field2);
       valid = valid && validateListItem(field2,j,"project report",format="text");
       //window.console && console.log('Ready for Github links: ' + field3);
       //valid = valid && validateListItem(field3,j,"Github link",format="text");
    });
    window.console && console.log('Javascript is complete.');
    return valid;
}
function checkEmptyBoxes(text_info, field, field_alias){
  if ( text_info == null || text_info == "") {
         triggerAlert("A " + field_alias + " is incomplete.", true);
         flagDataEntryBox(field);
         return false;
  }
  var trimmed = text_info.trim();
  if ( trimmed == null || trimmed == "") {
      const euro_capped = field_alias.charAt(0).toUpperCase() + str.slice(1);
      triggerAlert(euro_capped+"-" + j +
         "  still incomplete.", true);
      flagDataEntryBox(field);
      return false;
  }
}
function doValidateActivities() {
    //submitted = true;
    window.console && console.log('ready for profile: ');
    var languages = document.getElementById('languages').value;
    var computer = document.getElementById('computer').value;
    var pub = document.getElementById('publication').value;
    var licen = document.getElementById('licenses').value;

    valid_lang = checkEmptyBoxes(languages, 'languages', 'language');
    if (!valid_lang)
    {
         triggerAlert("Language entry is empty. Please try again.", true);
         return false;
    }
    valid_computer = checkEmptyBoxes(languages, 'languages', 'language');
    if (!valid_computer)
    {
         triggerAlert("Computer skill entry is empty. Please try again.", true);
         return false;
    }
    valid_pub = checkEmptyBoxes(languages, 'languages', 'language');
    if (!valid_pub)
    {
         triggerAlert("Publication entry is empty. Please try again.", true);
         return false;
    }
    valid_license = checkEmptyBoxes(languages, 'languages', 'language');
    if (!valid_license)
    {
         triggerAlert("License entry is empty. Please try again.", true);
         return false;
    }
    window.console && console.log('Ready for hobbies: ');
    hobby_size = hobby_array.length;
    // Hobbies
     len = hobby_array.length;
     for(i=0; i < hobby_size; i++){
          var j = i + 1;
          var field = hobby_array[i];
          var p = document.getElementById(field);
          window.console && console.log('Ready for hobbies: ' + field +
            ' of value ' + p.value);
          if (p===null)
          {
             triggerAlert("A hobbie is incomplete.", true);
             flagDataEntryBox(field);
             return false;
          }
          var text_info = p.value;
          if ( text_info == null || text_info == "") {
                 triggerAlert("A hobby is incomplete.", true);
                 flagDataEntryBox(field);
                 return false;
          }
          var text_info = p.value.trim();
          if ( text_info == null || text_info == "") {
              triggerAlert("Hobby-" + j +
                 "  still incomplete.", true);
              flagDataEntryBox(field);
              return false;
          }
      } // end of for loop
      window.console && console.log('Javascript is complete.');
  for(i=0; i< 100; i++){
  }
  return true;
}
function doValidateContacts() {
    submitted = true;
    var p = document.getElementById(id=last_text_box);
    //triggerAlert("The last box was " + last_text_box);
    var p_id = $(p).attr("id");
    window.console && console.log('The last box id attribute was ' + p_id);
    var text_info = p.value;
    if ( text_info == null || text_info == "") {
          triggerAlert("At least one entry box is incomplete. ",true );
          flagDataEntryBox(last_text_box);
          return false;
    }
    var text_info = p.value.trim();
    // if ( text_info == null || text_info == "") {
    //     triggerAlert("The last entry box you clicked is still empty. ",true );
    //     flagDataEntryBox(last_text_box);
    //     return false;
    // }
    //window.console && console.log('info is '+text_info);
   var len = text_info.length;
   myresult = "blank";
   phone = document.getElementById('ph').value;
   addr = document.getElementById('em').value;
     //profession = document.getElementById('pf').value;
     //goals = document.getElementById('gl').value;
   if (addr == null || addr == "" )
   {
         triggerAlert("e-mail is required.",true);
         return false;
   }
   phone = phone.trim();
   addr = addr.trim();
   if(!validateEmail(form1.email)){
        // Search with regular expression.
        // Offensive language check of parts at website.
        return false;
   }
   //     skillSet
   contactnum = contact_array.length;
   //triggerAlert("The skill count is ", skillnum);
   for(i=0; i < contactnum; i++){
     var j = i + 1;
     var field = contact_array[i];
     var p = document.getElementById(field);
     if (p===null)
     {
        continue;
     }
     var text_info = p.value;
     //alert(" For skill "+field+ ", the info is "+ text_info);
     if ( text_info == null || text_info == "") {
         triggerAlert("Contact " + field + " is incomplete.", true);
         flagDataEntryBox(contact_array[i]);
         return false;
     }
     var text_info = p.value.trim();
     if ( text_info == null || text_info == "") {
          triggerAlert("A contact is still incomplete.", true);
          flagDataEntryBox(contact_array[i]);
          return false;
     }
  } // end of for loop
  return true;
}
function validateApplication() {
    uhint = document.getElementById('hint').value.trim();
    uname = document.getElementById('username').value.trim();
    em_addr = document.getElementById('email').value.trim();
    if (uname == null || uname == "" ||
        em_addr == null || em_addr == "" ||
        uhint == null || uhint == "" )
    {
        triggerAlert("Name, email, and hint are required.", true);
        return false;
    }
    var len_hint = uhint.length;
    if( len_int < 5){
        triggerAlert("Hint is too short. Five characters are required.", true);
        return false;
    }
    if(!validateEmail(apply.email)){
                 return false;
    }
    return true;
}
function triggerAlert(message, replace=false){
  var source = $('#message_template').html();
  if(replace){
      $('#message').remove();
      $('#message_field').append(
        '<span style="float:left; margin:12px 12px 20px 0;" id="message">' +
                     message + ' </span>');
      } else {
        $('#message_field').append(
           '<span style="float:left; margin:12px 12px 20px 0;" id="message">' + message + '</span>');
    }
    $( function() {
      $( "#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
          "OK" : function() {
            $( this ).dialog( "close" );
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });
    } );
}
function checkLanguage(element_id) {
    var n = audit_array.includes(element_id);
    box_list_size = audit_array.length;
    if
      (
        n==false &&
        (! element_id.includes("year")) && (! element_id.includes("yr"))
        //&&
        //(! element_id.includes("school")) && (! element_id.includes("award"))
      )
      {
        audit_array.push(element_id)
        // audit_array is a stack of text box ID numbers.
        // It is used to select the next text box to be audited.
        // The first element of the next_text_box[0] is selected and then
        // removed from the stack.
        window.console && console.log(element_id + " was added. Audit-Array: " + audit_array);
      }
    if
      (
        !(element_id in audit_list)
         //&&
        //(! element_id.includes("year")) && (! element_id.includes("yr"))
         //&&
        //(! element_id.includes("school")) && (! element_id.includes("award"))
      )
      {
        audit_list[element_id] = -1;
      }
    p = document.getElementById(id=test_box);
    if (p===null)
    {
        var index = audit_array.indexOf(test_box);
        audit_array.splice(index, 1);
        test_box = 'fn';
        return false;
    }
    thisPhrase = document.getElementById(id=test_box).value;
    if (thisPhrase === null || thisPhrase == "" )
    {
        if(audit_array.includes(test_box) && (audit_array.length > 1)){
           var index = audit_array.indexOf(test_box);
           audit_array.splice(index, 1);
           test_box = audit_array[0];
        }
        return false;
    }
    if (thisPhrase == null || thisPhrase == "" )
    {
      if(audit_array.includes(test_box) && (audit_array.length > 1)){
         var index = audit_array.indexOf(test_box);
         audit_array.splice(index, 1);
         test_box = audit_array[0];
      }
      return false;
    }
    var phrase_len = thisPhrase.length;
    if(phrase_len > 0) {
       thisPhrase = thisPhrase.trim();
    }
    if(phrase_len < 3 || audit_list[test_box]==phrase_len) {
        var index = audit_array.indexOf(test_box);
        if(audit_array.includes(test_box) && (audit_array.length > 1)){
            audit_array.splice(index, 1);
        }
        test_box = audit_array[0];
        return false;
    }
    window.console && console.log("Check language triggered for " + test_box);
    checkJsonDictionary(test_box).then(function(result){
       eid = '#'+ test_box;
       if (result == "bad" ) {
              window.console && console.log(" processing bad data ... ");
              $(eid).css("borderWidth", "1px");
              $(eid).css("background-color", "bisque");
              $(eid).css("border-color", "#980200");
              $(eid).val(thisPhrase + " ... language filter triggered. ");
              if(audit_array.includes(test_box) && (audit_array.length > 1)){
                   var index = audit_array.indexOf(test_box);
                   audit_array.splice(index, 1);
              }
              audit_list[test_box] = audit_list[test_box] - 10000;
              window.console && console.log(" Bad result for. " + test_box);
              test_box = audit_array[0];
              window.console && console.log(" New test box will be " + test_box);
              return false;
       }
       //$(eid).css("background-color", 'rgb(249, 255, 185)');
       //$(eid).css("borderWidth", "2px");
       //$(eid).css("border-color", "#886600");
        //window.console && console.log(" language filter passed test "+result);
        window.console && console.log(" Good test result for. " + test_box);
        if(audit_array.includes(test_box) && (audit_array.length > 1)){
             audit_list[test_box] = phrase_len;
             var index = audit_array.indexOf(test_box);
             audit_array.splice(index, 1);
        }
        test_box = audit_array[0];
        window.console && console.log(" New test box will be " + test_box);
        return true;
    });
}
// checkJsonDictionary is the nested function within checkLanguage()
// which handles the JSON request.
function checkJsonDictionary(element_id) {
    //console.log('Checking online JSON dictionary ...');
    return new Promise(function(resolve, reject) {
      var p = document.getElementById(element_id);
      var text_info = p.value;
      var num = parseInt(text_info);
      if (! isNaN(num)) {
            return "good";
      }
        //window.console && console.log('info is '+text_info);
      var len = text_info.length;
        //window.console && console.log('The string length is ' + len);
      if( len > 2){
             $.getJSON('jsonLanguage.php?ter'+'m='+text_info).then(function(data) {
                resolve(data.first);
             });
       } else {
              return "good";
       }
    });
}
function validateEmail(mail)
{
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail.value))
  {
    return (true);
  }
    triggerAlert("You have entered an invalid email address!",true);
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
                eid = '#'+'fn';
                if (data == "bad" ) {
                  $(eid).css("borderWidth", "2px");
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
                  $(eid).css("borderWidth", "2px");
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
function flagDataEntryBox(box_id){
     eid = '#'+ box_id;
     if (typeof(box_id) != "undefined") {
       $(eid).css("borderWidth", "2px");
       $(eid).css("background-color", "bisque");
       $(eid).css("border-color", "#BB0200");
       //alert("Marking up " + eid + ".");
       $(eid).val("Incomplete");
     }
}
function flagNumberEntryBox(box_id){
     eid = '#'+ box_id;
     if (typeof(box_id) != "undefined") {
       $(eid).css("borderWidth", "2px");
       $(eid).css("background-color", "bisque");
       $(eid).css("border-color", "#BB0200");
     }
}
function reformatDataEntryBox(box_id){
      eid = '#'+ box_id;
      $(eid).css("background-color", 'rgb(249, 255, 185)');
      $(eid).css("borderWidth", "2px");
      $(eid).css("border-color", "#886600");
}
function showWidth( ele, w ) {
    triggerAlert(" The shoWidth function was called "+ w, true);
    $( "#shoWidth" ).text( "The width for the " + ele + " is " + w + "px." );
}
function makeActivityArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "activity"+j;
    }
    return a;
}
function makeContactArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "contact_id"+j;
    }
    return a;
}
function makeSkillArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "jobskill"+j;
        //val = document.getElementById(a[i]).value;
    }
    return a;
}
function makeSchoolArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "school"+ j;
    }
    return a;
}
function makeCertificationSchoolArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "certif_school"+ j;
    }
    return a;
}
function makeCertificationEduProviderArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "internet_edu"+ j;
    }
    return a;
}
function makeAwardArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "award"+ j;
    }
    return a;
}
function makeCertificationArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "certif"+ j;
    }
    return a;
}
function makeCertificationLinkArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "certif_link"+ j;
    }
    return a;
}
function makeProjectArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "proj"+ j;
    }
    return a;
}
function makeProjectReportArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "proj_report_lnk"+ j;
    }
    return a;
}
function makeGithubArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "git"+ j;
    }
    return a;
}
function makeJobYearStartArray(n){
    var a = [];
    for(i=0; i<n; i++){
          j = i + 1;
        a[i] = "wrk_start_yr"+ j;
    }
    return a;
}
function makeJobYearFinalArray(n){
    var a = [];
    for(i=0; i<n; i++){
          j = i + 1;
        a[i] = "wrk_final_yr"+ j;
    }
    return a;
}
function makeOrgArray(n){
    var a = [];
    for(i=0; i<n; i++){
          j = i + 1;
        a[i] = "org"+ j;
    }
    return a;
}
function makePositionDescArray(n){
    var a = [];
    for(i=0; i<n; i++){
          j = i + 1;
        a[i] = "summary"+ j;
    }
    return a;
}
function makePositionTitleArray(n){
    var a = [];
    for(i=0; i<n; i++){
          j = i + 1;
        a[i] = "title"+ j;
    }
    return a;
}
function makeHobbyArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "hobby"+j;
    }
    return a;
}
function deleteFromAuditList(field){
  if ( field in audit_list )
    {
      delete audit_list[field];
    }
  if (audit_array.includes(field) )
    {
      var index = audit_array.indexOf(field);
      audit_array.splice(index, 1);
    }
}
// remove from audit lists
function deleteSkill(group,field){
    $(group).remove();
    skill_removed = skill_removed + 1;
    var index = skill_array.indexOf(field);
    skill_array.splice(index, 1);
    deleteFromAuditList(field);
}
function deleteHobby(group,field){
    $(group).remove();
    hobby_removed = hobby_removed + 1;
    var index = hobby_array.indexOf(field);
    hobby_array.splice(index, 1);
    deleteFromAuditList(field);
}
function deleteContact(group,field){
    $(group).remove();
    contact_removed = contact_removed + 1;
    var index = contact_array.indexOf(field);
    contact_array.splice(index, 1);
    deleteFromAuditList(field);
}
// remove from audit lists
function deleteActivity(group, activity_field){
    //triggerAlert('delete activity', true);
    $(group).remove();
    //$('#activity_div1').remove();
    activity_removed = activity_removed + 1;
    //window.console && console.log('Removed group is = ' + group);
    //window.console && console.log('Removed activity is = ' + activity_field);
    var index = activity_array.indexOf(activity_field);
    activity_array.splice(index, 1);
    deleteFromAuditList(activity_field);
}
// remove from audit lists
function deleteEdu(group,schoolfield,award_field){
    window.console && console.log('Removed group is = ' + group);
    $(group).remove();
    //window.console && console.log('Removed group is = ' + group);
    edu_removed = edu_removed + 1;
    var index1 = school_array.indexOf(schoolfield);
    school_array.splice(index1, 1);
    var index2 = award_array.indexOf(award_field);
    award_array.splice(index2, 1);
    deleteFromAuditList(schoolfield);
    deleteFromAuditList(award_field);
}
function deleteCertif(group,provider,school,award,link){
    window.console && console.log('The group for removal is ' + group);
    $(group).remove();
    certif_removed = certif_removed + 1;
    var index1 = certif_edu_provider_array.indexOf(provider);
    certif_edu_provider_array.splice(index1, 1);
    var index2 = certif_school_array.indexOf(school);
    certif_school_array.splice(index2, 1);
    var index3 = certif_array.indexOf(award);
    certif_array.splice(index3, 1);
    var index4 = certif_lnk_array.indexOf(link);
    //window.console && console.log('The unreduced list is ' + certif_lnk_array);
    certif_lnk_array.splice(index4, 1);
    //window.console && console.log('The reduced list is ' + certif_lnk_array);
    deleteFromAuditList(provider);
    deleteFromAuditList(school);
    deleteFromAuditList(award);
    deleteFromAuditList(link);
}
function deleteProject(group,proj_field,report_lnk_field,github_field){
    window.console && console.log('The group for removal is ' + group);
    triggerAlert("Project removal for " + group, true );
    $(group).remove();
    triggerAlert("Project removal attempted. ", true );
    proj_removed = proj_removed + 1;
    var index1 = proj_array.indexOf(proj_field);
    proj_array.splice(index1, 1);
    var index2 = proj_report_array.indexOf(report_lnk_field);
    proj_report_array.splice(index2, 1);
    var index3 = github_array.indexOf(github_field);
    github_array.splice(index3, 1);
    deleteFromAuditList(proj_field);
    deleteFromAuditList(report_lnk_field);
    //deleteFromAuditList(github_field);
}
function deleteJob(group,year1_field, year2_field,org,title,summary){
    $(group).remove();
    position_removed = position_removed + 1;
    var index1 = org_array.indexOf(org);
    org_array.splice(index1, 1);
    var index2 = position_title_array.indexOf(title);
    position_title_array.splice(index2, 1);
    var index3 = position_desc_array.indexOf(summary);
    position_desc_array.splice(index3, 1);
    var index4 = org_year_start_array.indexOf(year1_field);
    org_year_start_array.splice(index4, 1);
    var index5 = org_year_final_array.indexOf(year2_field);
    org_year_final_array.splice(index5, 1);
    var index6 = position_title_array.indexOf(year2_field);
    position_title_array.splice(index6, 1);
    deleteFromAuditList(org);
    deleteFromAuditList(title);
    deleteFromAuditList(summary);
    deleteFromAuditList(year1_field);
}
function markVisited(Id) {
  obj = document.getElementById(Id);
  current = obj.className;
  if (obj.value==''){
  } else {
    //obj.className = current + ' visited';
  }
}
