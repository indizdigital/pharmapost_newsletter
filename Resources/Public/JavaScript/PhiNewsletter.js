define([], function() {
   var PhiNewsletter = {
   };

   PhiNewsletter.init = function() {
     // do init stuff
     var reloadFrame = document.getElementById("reloadBEFrame");
     if(reloadFrame){
       reloadFrame.addEventListener("click",function(){
         self.location.reload(true);
         return false;
       });
     }


     var h1 = document.querySelector("h1");
     if(h1 != null){
       //first step after choosing config
       if(h1.innerHTML.indexOf("Edit Newsletter Config") > -1){
         var editButton = document.querySelector("a.t3js-editform-close");
         var editButtonHtml = editButton.innerHTML;
         editButton.innerHTML = editButtonHtml.replace("Close","Weiter").replace("/typo3/sysext/core/Resources/Public/Icons/T3Icons/actions/actions-close.svg","/typo3conf/ext/phi_newsletter/Resources/Public/Icons/proceed.svg");

         var saveButton = document.querySelector("button[name='_savedok']");
         var saveButtonHtml = saveButton.innerHTML;
         saveButton.innerHTML = saveButtonHtml.replace("Save","Speichern");
       }
       //first step after choosing config
       if(h1.innerHTML.indexOf("Newsletter Config") > -1){
         var newButton = document.querySelector("a.t3js-editform-new");
         if(newButton != null){
           document.querySelector("a.t3js-editform-new").remove();
         }
         var deleteButton = document.querySelector("a.t3js-editform-delete-record");
         if(deleteButton != null){
           document.querySelector("a.t3js-editform-delete-record").remove();
         }
       }
       //snd step: choose article
       if(h1.innerHTML.indexOf("Newsletter versenden") > -1){

         var proceedButton = document.querySelector(".proceedButton");
         if(proceedButton != null){
           var proceedText = proceedButton.value;
           //proceed button
           var sendButton = document.querySelector("a.t3js-editform-close");
           var sendButtonHtml = sendButton.innerHTML;
           sendButton.innerHTML = sendButtonHtml.replace("Weiter",proceedText);
           sendButton.addEventListener("click",function(){
             PhiNewsletter.tryToSend();
           });
           //proceedButton is sending is shown on choosing group template
           if(proceedButton.classList.contains("sendCampaign")){

             //remove send button
             var sendButton = document.querySelector("a.t3js-editform-close").remove();
             //send draft button
             var proceedDraft = sendButton.cloneNode(true);
             proceedDraft.id = "sendAsDraftButton";
             proceedDraft.innerHTML = proceedDraft.innerHTML.replace(proceedText,"Entwurf senden");
             proceedDraft.addEventListener("click",function(){
               if(document.getElementById("sendAsDraft")){
                 document.getElementById("sendAsDraft").value = 0;
               }
               PhiNewsletter.tryToSend();
             });
             sendButton.parentElement.appendChild(proceedDraft);
           }

         }else{
           console.log(proceedButton)
         }
       }
     }
     //document.querySelector("a.t3js-editform-close img")[0];//.src = "../typo3conf/ext/phi_newsletter/Resources/Public/Icons/proceed.svg";

   };
   PhiNewsletter.tryToSend = function() {
     var groups = document.querySelectorAll("#selectGroupTable input[name='tx_phinewsletter_web_phinewsletterphinewsletter[groups][]']");
     var selectedOne = false;
     for (i = 0; i < groups.length; i++) {
        if(groups[i].checked){
          selectedOne = true;
        }
      }
      if(groups.length){
        if(selectedOne){
          document.querySelector("form").submit();
        }
      }else{
        document.querySelector("form").submit();
      }
   };
   PhiNewsletter.init();

   // To let the module be a dependency of another module, we return our object
   return PhiNewsletter;
});
