function getcube(){
var number=document.getElementById("number").value;
alert(number*number*number);
}
function doValidate() {
    //submitted = true;
    var p = document.getElementById(last_text_box);
    var text_info = p.value;
    //var  number=document.getElementById("number").value;
    //var tagId = $(this).attr(id);
    var p_id = $(p).attr("id"); // e.g.; hobby1
    //var eid = '#'+last_text_box; // not used here; no pound sign for argument
    //var htmlcode = p.innerHTML;
    var text_info = p.value.trim();
    window.console && console.log('the last text box variable is ' +
                                                                last_text_box);
    window.console && console.log('The retrieved tag id is ' + p_id);
    if ( text_info == null || text_info == "") {
          triggerAlert("At least one entry box is incomplete. ", true );
          flagDataEntryBox(last_text_box);
          return false;
    }
    window.console && console.log('The contents of ' + p_id +
                                             ' are ' + text_info);
    // if ( text_info == null || text_info == "") {
    //     triggerAlert("The last entry box you clicked is still empty. ",true );
    //     flagDataEntryBox(last_text_box);
    //     return false;
    // }
    //window.console && console.log('info is '+text_info);
    var len = text_info.length;
    myresult = "blank";
   //  if( Boolean(len > 2) && len > 2){
   //      try
   //      {
   //         window.console && console.log('Ready for ajax ' + len);
   //         $.ajax({
   //             dataType: "json",
   //             url: "jsonLanguage.php?ter"+"m="+text_info,
   //             success: function(data) {
   //                  myresult = data.first;
   //             },
   //             async: false
   //         });
   //         // window.console && console.log('my result is '+ myresult);
   //         if (myresult == "bad") {
   //            $(eid).css("borderWidth", "2px");
   //            $(eid).css("background-color", "bisque");
   //            $(eid).css("border-color", "#980200");
   //            $(eid).val(text_info+" . . . Language filter was triggered.");
   //            triggerAlert("Language filter for the last modified text box was triggered.",true);
   //            return false;
   //         }
   //         $(eid).css("background-color", 'rgb(249, 255, 185)');
   //         $(eid).css("borderWidth", "2px");
   //         $(eid).css("border-color", "#886600");
   //       } catch(e) {
   //          triggerAlert("Something went wrong. Please try again.",true);
   //          return false;
   //       }
   // }
   window.console && console.log('ready for profile: ');
   first_name = document.getElementById('fn').value;
   last_name = document.getElementById('ln').value;
   addr = document.getElementById('em').value;
     //profession = document.getElementById('pf').value;
     //goals = document.getElementById('gl').value;
   if (first_name == null || first_name == "" ||
       last_name == null || last_name == "" ||
           addr == null || addr == "" )
         // || profession == null || profession == ""
         // || goals == null || goals == ""
   {
         triggerAlert("Name and email are required.",true);
         return false;
   }
   first_name = first_name.trim();
   last_name = last_name.trim();
   addr = addr.trim();
   if (first_name == null || first_name == "" ||
       last_name == null ||  last_name == "" ||
           addr == null ||      addr == ""   )
   {
        triggerAlert("Name and email are required. Please try again.", true);
        return false;
   }
   if(!validateEmail(form1.email)){
        // Search with regular expression.
        // Offensive language check of parts at website.
        return false;
   }
   //     skillSet
   window.console && console.log('ready for skills: ');
   skillnum = skill_array.length;
   //triggerAlert("The skill count is ", skillnum);
   for(i=0; i<skillnum; i++){
     var j = i + 1;
     var field = skill_array[i];
     var p = document.getElementById(field);
     if (p===null)
     {
        continue;
     }
     var text_info = p.value;
     //alert(" For skill "+field+ ", the info is "+ text_info);
     if ( text_info == null || text_info == "") {
         triggerAlert("A Skill is incomplete.", true);
         flagDataEntryBox(skill_array[i]);
         return false;
     }
     var text_info = p.value.trim();
     if ( text_info == null || text_info == "") {
          triggerAlert("A Skill is still incomplete.", true);
          flagDataEntryBox(skill_array[i]);
          return false;
     }
  } // end of for loop
//            education
  window.console && console.log('ready for education: ');
  school_size = school_array.length;
  for(i=0; i < school_size; i++){
      var j = i + 1;
      var field = school_array[i];
      var p = document.getElementById(field);
      if (p===null)
      {
         continue;
      }
      var text_info = p.value;
   // alert(" For school "+field+ ", the info is "+ text_info);
      if ( text_info == null || text_info == "") {
             triggerAlert("A School name in education is incomplete.", true);
             flagDataEntryBox(school_array[i]);
             return false;
      }
      var text_info = p.value.trim();
      if ( text_info == null || text_info == "") {
           triggerAlert("A School name in education is still incomplete.", true);
           flagDataEntryBox(school_array[i]);
           return false;
       }
   } // end of for loop
   //          Educational Awards
   award_len = award_array.length;
   for(i=0; i < award_len; i++){
        var j = i + 1;
        var field = award_array[i];
        var p = document.getElementById(field);
        if (p===null)
        {
           triggerAlert("An Educational Award is incomplete.", true);
           flagDataEntryBox(field);
           return false;
        }
        var text_info = p.value;
        if ( text_info == null || text_info == "") {
               triggerAlert("An Educational Award is incomplete.", true);
               flagDataEntryBox(field);
               return false;
        }
        var text_info = p.value.trim();
        if ( text_info == null || text_info == "") {
            triggerAlert("Educational Award-" + j +
               "  still incomplete.", true);
            flagDataEntryBox(field);
            return false;
        }
    } // end of for loop
    // work history
    window.console && console.log('ready for work history: ');
    org_len = org_array.length;
    for(i=0; i < org_len; i++){
         var j = i + 1;
         var field = org_array[i];
         var p = document.getElementById(field);
         if (p===null)
         {
            continue;
         }
         var text_info = p.value;
         if ( text_info == null || text_info == "") {
             triggerAlert("Organization-" + j +
               "  of the work history is incomplete.", true);
             flagDataEntryBox(org_array[i]);
             return false;
         }
         var text_info = p.value.trim();
         if ( text_info == null || text_info == "") {
             triggerAlert("Organization-" + j +
                 "  of the work history is still incomplete.", true);
             flagDataEntryBox(org_array[i]);
             return false;
         }
    } // end of for loop
    len = org_year_start_array.length;
    for(i=0; i < len; i++){
          var j = i + 1;
          var field = org_year_start_array[i];
          window.console && console.log('Processing start year for '+field);
          var p = document.getElementById(field);
          if (p===null)
          {
             continue;
          }
          var text_info = p.value;
          if ( text_info == null || text_info == "") {
               triggerAlert("Start Year-" + j +
                     "  of the work history is incomplete.", true);
               flagDataEntryBox(field);
               return false;
          }
          var text_info = p.value.trim();
          if ( text_info == null || text_info == "") {
               triggerAlert("Start Year-" + j +
                   "  of the work history is still incomplete.", true);
               flagDataEntryBox(field);
               return false;
          }
          var num = parseInt(text_info);
          if ( isNaN(num)) {
               flagDataEntryBox(field);
               triggerAlert("Start Year-" + j +
                   " of the work history requires a 4-digit year.", true);
               return false;
          }
          if ( num < 1000 || num > 9999) {
              flagDataEntryBox(field);
              triggerAlert("Start Year-" + j +
                   "  of the work history must be a 4-digit integer.", true);
              return false;
          }
    } // end of for loop for start array
    year_final = org_year_final_array.length;
    for(i=0; i < year_final; i++){
            var j = i + 1;
            var field = org_year_final_array[i];
            var p = document.getElementById(field);
            if (p===null)
            {
               continue;
            }
            var text_info = p.value;
            // The final year may be incomplete
            if ( text_info == null || text_info == "") {
                // triggerAlert("Final Year-" + j +
                //    " of the work history is incomplete.", true);
                // flagDataEntryBox(field);
                // return false;
                continue;
            }
            var text_info = p.value.trim();
            if ( text_info == null || text_info == "") {
                 // triggerAlert("Final Year-" + j +
                 //     " of the work history is still incomplete.", true);
                 // flagDataEntryBox(field);
                 // return false;
                 continue;
            }
            var num = parseInt(text_info);
            if ( isNaN(num)) {
                 // flagDaEntryBox(field);
                 // triggerAlert("Final Year-" + j +
                 //     " of the work history requires a 4-digit year.", true);
                 // return false;
                 continue;
            }
            if ( num < 1000 || num > 9999) {
                flagDataEntryBox(field);
                triggerAlert("Final Year-" + j +
                  " of the work history must be a 4-digit integer.", true);
                return false;
            }
      } // end of for loop for final work year
      descnum = position_desc_array.length;
      for(i=0; i< descnum; i++){
          var j = i + 1;
          var field = position_desc_array[i];
          var p = document.getElementById(field);
          if (p===null)
          {
             continue;
          }
          var text_info = p.value;
          if ( text_info == null || text_info == "") {
               triggerAlert("Job Description-" + j +
                     " of the work history is incomplete.", true);
               flagDataEntryBox(position_desc_array[i]);
               return false;
          }
          var text_info = p.value.trim();
          if ( text_info == null || text_info == "") {
               triggerAlert("Job Description-" + j +
                      " of the work history is still incomplete.", true);
               flagDataEntryBox(position_desc_array[i]);
               return false;
          }
      } // end of for loop for position description
  window.console && console.log('Javascript is complete.');
  for(i=0; i< 1000000; i++){
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
    //if( Boolean(len > 2) && len > 2){
        //try
        //{
          // window.console && console.log('Ready for ajax ' + len);
         //   $.ajax({
         //       dataType: "json",
         //       url: "jsonLanguage.php?ter"+"m="+text_info,
         //       success: function(data) {
         //            myresult = data.first;
         //       },
         //       async: false
         //   });
         //   // window.console && console.log('my result is '+ myresult);
         //   if (myresult == "bad") {
         //      $(eid).css("borderWidth", "2px");
         //      $(eid).css("background-color", "bisque");
         //      $(eid).css("border-color", "#980200");
         //      $(eid).val(text_info+" . . . Language filter was triggered.");
         //      triggerAlert("Language filter for the last modified text box was triggered.",true);
         //      return false;
         //   }
         //   $(eid).css("background-color", 'rgb(249, 255, 185)');
         //   $(eid).css("borderWidth", "2px");
         //   $(eid).css("border-color", "#886600");
         // } catch(e) {
         //    triggerAlert("Something went wrong. Please try again.",true);
         //    return false;
         // }
   //}
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
function triggerAlert(message,replace=false){
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
              $(eid).css("borderWidth", "2px");
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
       $(eid).css("background-color", 'rgb(249, 255, 185)');
       $(eid).css("borderWidth", "2px");
       $(eid).css("border-color", "#886600");
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
function flagDataEntryBox(box_id){
     eid = '#'+ box_id;
     $(eid).css("borderWidth", "2px");
     $(eid).css("background-color", "bisque");
     $(eid).css("border-color", "#BB0200");
     $(eid).val("Incomplete");
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
        a[i] = "job_skill"+j;
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
function makeAwardArray(n){
    var a = [];
    for(i=0; i<n; i++){
        j = i + 1;
        a[i] = "award"+ j;
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
        a[i] = "company"+ j;
    }
    return a;
}
function makePositionDescArray(n){
    var a = [];
    for(i=0; i<n; i++){
          j = i + 1;
        a[i] = "position_desc"+ j;
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
function deleteActivity(group, activity_field){
    //triggerAlert('delete activity', true);
    $(group).remove();
    //$('#activity_div1').remove();
    activity_removed = activity_removed + 1;
    window.console && console.log('Removed group is = ' + group);
    window.console && console.log('Removed activity is = ' + activity_field);
    var index = activity_array.indexOf(activity_field);
    activity_array.splice(index, 1);
    deleteFromAuditList(activity_field);
}
function deleteEdu(group,schoolfield,award_field){
    $(group).remove();
    edu_removed = edu_removed + 1;
    var index1 = school_array.indexOf(schoolfield);
    school_array.splice(index1, 1);
    var index2 = award_array.indexOf(award_field);
    award_array.splice(index2, 1);
    deleteFromAuditList(schoolfield);
    deleteFromAuditList(award_field);
}
function deleteJob(group,year1_field, year2_field,orgfield,descfield){
    $(group).remove();
    position_removed = position_removed + 1;
    var index1 = org_array.indexOf(orgfield);
    org_array.splice(index1, 1);
    var index2 = position_desc_array.indexOf(descfield);
    position_desc_array.splice(index2, 1);
    var index3 = org_year_start_array.indexOf(year1_field);
    org_year_start_array.splice(index3, 1);
    var index4 = org_year_final_array.indexOf(year2_field);
    org_year_final_array.splice(index4, 1);
    deleteFromAuditList(orgfield);
    deleteFromAuditList(descfield);
}
function adjustWindow(){
  var w = $( window ).width();
  if ( w > 1100) {
      $("#main").css("left", "25%");
      $("#main").css("right", "25%");
      $("#main").each(function () {
          this.style.setProperty( "width", "50%");
          //this.style.setProperty( "width", "40%", "important" );
      });
      $("h1").css("font-size", "1.8rem");
      $(".button-submit").css("font-size", "1.4rem");
  } else {
      $("#top_left").hide();
      $("#top_right").hide();
  }
  if (w > 900 && w < 1101) {
      $("#main").css("left", "20%");
      $("#main").css("right", "20%");
      $("#main").css("width", "60%");
      $("h1").css("font-size", "1.7rem");
      $(".button-submit").css("font-size", "1.7rem");
  }
  if (w > 700 && w < 901) {
      $("#main").css("left", "10%");
      $("#main").css("right", "10%");
      $("#main").css("width", "80%");
      $("h1").css("font-size", "1.6rem");
      $(".button-submit").css("font-size", "1.6rem");
  }
  if (w > 500 && w < 701) {
      $("#main").css("left", "5%");
      $("#main").css("right", "5%");
      $("#main").css("width", "90%");
      $("h1").css("font-size", "1.5rem");
      $(".button-submit").css("font-size", "1.5rem");
  }
  if (w > 400 && w < 501) {
      window.console && console.log('Found small device; width = ' + w);
      $("h1").css("font-size", "1.4rem");
      $(".button-submit").css("font-size", "1.4rem");
      $("#main").css({"left":"3%","right":"3%"});
      $("#main").each(function () {
               this.style.setProperty( "width", "94%", "important" );
      });
  }
  if (w < 401) {
      window.console && console.log('Found small device; width = ' + w);
      $("h1").css("font-size", "1.3rem");
      $(".button-submit").css("font-size", "1.3rem");
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
