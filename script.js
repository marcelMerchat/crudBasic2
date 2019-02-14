function doValidate() {
        //console.log('Validating...');
        try {
          firstname = document.getElementById('fn').value;
          lastname = document.getElementById('ln').value;
          addr = document.getElementById('em').value;
          profession = document.getElementById('pf').value;
          goals = document.getElementById('gl').value;
          if (firstname == null || firstname == "" || lastname == null || lastname == ""
                                         ||
              addr == null || addr == "" || profession == null || profession == ""
                                         ||
              goals == null || goals == "" ) {
                alert("All fields must be filled out");
                return false;
            }
            if ( addr.indexOf('@') == -1 ) {
                alert("Invalid email address");
                return false;
            }
            return true;
        } catch(e) {
            return false;
        }
        return false;
}
// function checkLanguage() {
//         //console.log('Validating...');
//         try {
//           $(document).on('click', '.goal-box', 'input[type="text"]', function(){
//                   var textboxId = $(this).attr("id");
//                   txtentry = document.getElementById(id=textboxId).value;
//                   //var value="This is a string";
//                   var len = txtentry.length;
//                   if( len > 0){
//                       $.getJSON('jsonLanguage.php?txtentr'+'y='+txtentry, function(data) {
//                           var y = data;
//                           $.each(data, function(i, field){
//                             $('#gl'.goal-box-layout").append(field + " ");
//
//                           });
//                           //$('.goal-box').autocomplete({ source: y });//                           //if(y == true){
//                           //document.getElementById(textboxId).innerHTML = txtentry + y;
//                           document.getElementById(textboxId).innerHTML = txtentry + " new";
//                           document.getElementById(textboxId).style.color = "#FF0000";
//
//                           //}
//                       });
//                   }
//           });
//
//           //$("button").click(function(){
//               //$.getJSON("demo_ajax_json.js", function(result){
//                   //$.each(result, function(i, field){
//                     //$("div").append(field + " ");
//                   //});
//               //});
//           //});
//           //goals = document.getElementById('gl').value;
//           //if (firstname == null || firstname == "" ) {
//             //goals == null || goals == "" )
//             //    alert("All fields must be filled out");
//               //  return false;
//           //}
//           return true;
//         } catch(e) {
//             return false;
//         }
//         return false;
// }
function removeSkill(cntSkill, removedCnt){
      //alert("skill count is " + cntSkill);
      removedCnt = removedCnt + 1;
      alert("removed skill count is " + (removedCnt));
      alert("skill removed, count decreases to " + (cntSkill - removedCnt));
      // Stored value of 'skillRemoved' is pre-incremented by one count.
      skillRemoved = removedCnt;
      // try1 = 0;
      // try2 = 0;
      // try2 += 1;
      // alert("try shortcut is "+try2);
      //return removedCount;
}
function removeEdu(cntEdu, removedCnt){
      //alert("skill count is " + cntSkill);
      removedCnt = removedCnt + 1;
      alert("removed education count is " + removedCnt);
      alert("An education has been removed, count decreases to " + (cntEdu - removedCnt));
      // Stored value of 'skillRemoved' is pre-incremented by one count.
      eduRemoved = removedCnt;
}
function removePosition(cntPosition, removedCnt){
      removedCnt = removedCnt + 1;
      alert("removed position count is " + removedCnt);
      alert("Position removed, count decreases to " + (cntPosition - removedCnt));
      positionRemoved = removedCnt;
}
// insert HTML in the DOM
// if(isMobileDevice) {
//     document.write('<div class="less-bottom-margin year-input-label left">Year1</div>');
//
//     document.write('<input class="year-entry-box" type="text" name="edu_year'+ 10 + '" />');
// } else {
//       document.write('<div class="container-form-entry">');
//       document.write('<div class="less-bottom-margin short-input-label left">Year2</div>');
//       document.write('<div class="less-top-margin less-bottom-margin box-profile-input">');
//       document.write('<input class="year-entry-box" type="text" name="edu_year'+ 10 +'" \
//       value="' + eduyear + '"/>');
//       //document.write('</div>');
// }

// <div class="container-form-entry">
//     <div class="less-bottom-margin short-input-label">Year: </div>
//     <div class="less-bottom-margin less-top-margin box-profile-input">
//          <input class="text-box" type="text" name="edu_year@COUNT@" value="" />
//     </div>
// </div>
