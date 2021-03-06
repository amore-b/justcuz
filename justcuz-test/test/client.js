  $(document)
    .ready(function() {
      var cid, points, userType = 0, cardNum = 0;
      var memName, addy, cardType, emailad = "";
      
      // fix main menu to page on passing
      $('.main.menu').visibility({
        type: 'fixed'
      });
      $('.overlay').visibility({
        type: 'fixed',
        offset: 80
      });
      // lazy load images
      $('.image').visibility({
        type: 'image',
        transition: 'vertical flip in',
        duration: 500
      });
      $('.ui.dropdown')
        .dropdown()
      ;
      $('.ui.modal')
        .modal()
      ;
      $('#loginModal').modal("setting", {
        onHide: function() {
          $('#loginForm').form('clear');
          $('#errorMsg').html("");
        }
      });
      $('#signUpModal').modal("setting", {
        onHide: function() {
          $('#signUpForm').form('clear');
          $('#formError').html("");
          $('#sqlMsg').html("");
        }
      }); 

      function drawTable(data, value){
        if ((data.length != undefined) && (data.length != 0)) {
          var keys = Object.keys(data[0]);
          var cards = $("<div class= 'ui center aligned special cards'>"); //change this back?
          
          for (var j = 0; j < data.length; j++) {
            cards.append($(drawCard(data[j],keys)));
          }
          cards.append($("</div>"));
          $('#catalogtest').html(cards);

          $('.image').dimmer({
            on: 'hover'
          });
        } else {
          drawNoResults();
        }

      }
    
      // labels is an array containing the attribute names
      function drawCard(cardData, labels) {
        var desc = "";
        if(cardData.GENDER)
          desc += cardData.GENDER + " ";
        if(cardData.COLOR)
          desc += cardData.COLOR + " ";
        if(cardData.TYPE)
          desc += cardData.TYPE + " ";        
        if(cardData.COMPANY_NAME)
          desc += "from " + cardData.COMPANY_NAME;

        var card = $("<a class='ui centered card' href='orderPage.html?inum=" + cardData.ITEM_NUM + "&points=" + points + "&cid="+ cid + "&memName=" + memName +"&email=" + emailad +"&addy=" + addy + "&cardNum=" + cardNum + "&cardType=" + cardType +"&price="+ cardData.PRICE  + "'>"+
                     "<div class='blurring dimmable image'>" +
                     "<div class='ui inverted dimmer'><div class='content'><div class='center'>" + 
                     "<div class='ui primary button'>Buy now</div><br/><br/>" + "<font color='black'>" + desc + " $" +cardData.PRICE+"</font>" +"</div></div></div>" +
                     "<img src='catalog/" + cardData.ITEM_NUM +".jpg'></div></a>");
        return card;        
      }

      function drawNoResults() {
          $('#catalog').html("<p>No results found.</p>");
      }

      $(function() {
          $('.ui.simple.dropdown').dropdown({
            onChange: function(value) {

              //var price = "";
              var type = "";
              var gender = "";
              var color = "";
              var company_name = "";
              var toshow = "";

              if ( $('#all').is( ":checked" )) {
                toshow = "/all";
                console.log("show all");
              } else {
                if ( $('#type').is( ":checked" )) {
                  type = "/type";
                  console.log("show type");
                }
                if ( $('#gender').is( ":checked" )) {
                  gender = "/gender";
                  console.log("show gender");
                }
                if ( $('#color').is( ":checked" )) {
                  color = "/color";
                  console.log("show color");
                }
                if ( $('#company_name').is( ":checked" )) {
                  company_name = "/company_name";
                  console.log("show company_name");
                }

                toshow = type + gender + color + company_name;
              
              }
              console.log(value);
              displayMerch(value, toshow);
            }
          });
      });

      function displayMerch (value, toShow) {
        $.ajax({
          url: "index7.php/merchandise/" + value + toShow ,
          success: function(result) {
              drawTable(result, value);
          },
          error: function() {
              console.log("nope");
          }
        })        
      }
     
      $('#loginForm')
        .form({
          fields: {
            email: {
              identifier  : 'email',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please enter your e-mail'
                },
                {
                  type   : 'email',
                  prompt : 'Please enter a valid e-mail'
                }
              ]
            },
            password: {
              identifier  : 'password',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please enter your password'
                },
                {
                  type   : 'length[6]',//change this
                  prompt : 'Your password must be at least 6 characters'
                }
              ]
            }
          }
        });

      $('#signUpForm')
        .form({
          fields: {
            name: {
              identifier  : 'name',
              rules: [ //maybe add one with max length or char type
                {
                  type   : 'empty',
                  prompt : 'Please enter your name'
                }
              ]
            },
            password: {
              identifier  : 'password',
              rules: [ //re-enter password and check they match is do-able
                {
                  type   : 'empty',
                  prompt : 'Please enter a password'
                }
              ]
            },                         
            email: {
              identifier  : 'email',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please enter your e-mail'
                },
                {
                  type   : 'email',
                  prompt : 'Please enter a valid e-mail'
                }
              ]
            },
            address: {
              identifier  : 'address',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please enter your address'
                }
              ]
            },            
            ctype: {
              identifier  : 'card-type',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please select a credit card type'
                }
              ]
            },
            cnum: {
              identifier  : 'card-number',
              rules: [
                {
                  type   : 'creditCard',
                  prompt : 'Please enter a valid credit card number'
                }
              ]
            }             
          }
         
        });        

        function checkForLoginCookie() {
          var cookie = readCookie("justcuz");
          if(cookie) {
            cid = cookie;
            emailad = readCookie("justcuz-addr");

            $.ajax({
              url: "index7.php/users/info/"+ emailad + "/" + cid,
              success: function(result) {
                if(result["NAME"]) {
                  addy = result["ADDRESS"];
                  points = result["POINTS"];
                  memName = result["NAME"];
                  emailad = result["EMAIL"];
                  cardNum = result["CARD_NUM"];
                  cardType = result["CARD_TYPE"];
                  userType = result["U_TYPE"];

                  createCookie("justcuz", cid, 1);
                  createCookie("justcuz-addr", result["EMAIL"], 1);

                  showLoginButton(false, result["NAME"]);
                }

                if(userType == "mgr") {
                  $('#secretSauce').show();
                  $('#ultraSecret').show();
                } else if(userType == "emp"){
                  $('#secretSauce').show();
                  $('#ultraSecret').hide();
                } else {
                  $('#secretSauce').hide();
                  $('#ultraSecret').hide();
                }
                displayMerch("type", "/all");
              },
              error: function() {
                console.log("nope");
              }
            })
          } else {
              $('#secretSauce').hide();
              $('#ultraSecret').hide();            
          }
        }

        function showLoginButton(show, name) {
          if(!show) {
            if(userType == "mem") {
              $('#loginButton').replaceWith("<div id='userArea'><i class='user icon'></i>" + name + "<a class='ui inverted basic button' href='member.html?value="+cid +"'>Profile</a><button class='ui inverted basic button' id='logoutButton'>Log out</button></div>");
            } else {
              $('#loginButton').replaceWith("<div id='userArea'><i class='user icon'></i>" + name + "<button class='ui inverted basic button' id='logoutButton'>Log out</button></div>");
            }
            $('#signUpButton').hide();
          } else {
            $('#userArea').replaceWith("<button class='ui inverted basic button' id='loginButton'>Log in</button><button class='ui inverted basic button' id='signUpButton'>Sign up</button>");
          }
        }

        $('#loginForm').submit(function(e) {
          e.preventDefault();
          
          if($('#loginForm').form('is valid')) {
            var email = $('#loginEmail').val(),
                password = $('#loginPassword').val();

            $.ajax({
              url: "index7.php/users/login",
              type: 'get',
              data: {"email": email, "password": password},
              success: function(result) {
                if(result["NAME"]) {
                  //id will be either cid or eid
                  cid = (result["CID"])? result["CID"] : result["EID"];
                  console.log(cid);

                  addy = result["ADDRESS"];
                  points = result["POINTS"];
                  memName = result["NAME"];
                  emailad = result["EMAIL"];
                  userType = result["U_TYPE"];
                  cardNum = result["CARD_NUM"];
                  cardType = result["CARD_TYPE"];

                  createCookie("justcuz", cid, 1);
                  createCookie("justcuz-addr", result["EMAIL"], 1);

                  if(userType == "mgr") {
                    $('#secretSauce').show();
                    $('#ultraSecret').show();
                  } else if(userType == "emp"){
                    $('#secretSauce').show();
                    $('#ultraSecret').hide();
                  } else {
                    $('#secretSauce').hide();
                    $('#ultraSecret').hide();
                  }

                  $('#loginModal').modal('hide');
                  showLoginButton(false, result["NAME"]);
                  displayMerch("type", "/all");
                } else {
                    //display login error message
                    console.log(result);
                }
              //todo: error case
              }
            })
          }
        })

        $('#signUpForm').submit(function(e) {
          e.preventDefault();
          
          if($('#signUpForm').form('is valid')) {
            var name = $('#fullName').val(),
                email = $('#emailAddr').val(),
                address = $('#mailingAddr').val(),
                password = $('#password').val(),
                cType = $('#cardType').val(),
                cNum = $('#cardNumber').val();

            $.ajax({
              url: "index7.php/users/new",
              type: 'get',
              data: {"email": email, "password": password, "name": name,
                     "address": address, "cardType": cType, "cardNum": cNum},
              success: function(result) {
                if(!result["error"]) {
                  console.log(result);
                  
                  cid = (result["CID"]);
                  addy = address;
                  points = result["POINTS"];
                  memName = name;
                  emailad = email;
                  userType = 2;                
                  cardNum = cNum;
                  cardType = cType;

                  createCookie("justcuz", cid, 1);
                  createCookie("justcuz-addr", email, 1);
     


                  $('#signUpModal').modal('hide');
                  showLoginButton(false, name);
                  displayMerch("type", "/all");
                } else {
                    $('#sqlMsg').html("<div class='ui error message' style='display:block; color:#9F3A38'><ul class='list'><li>"+result["error"]+"</li></ul></div>");
                    //display login error message
                    console.log(result);
                }
              //todo: error case
              }
            })
          }
        })

        $('#userNav').on('click', '#logoutButton', function(){
          points = 0;
          cid = undefined;


          //probably want a set function instead of changing values all over
          addy = undefined;
          memName = undefined;
          emailad = undefined;
          userType = undefined;
          cardType = undefined;
          cardNum = 0; //not sure that this matters, setting to what is at the top

          eraseCookie("justcuz");
          eraseCookie("justcuz-addr");
          showLoginButton(true);       


          //work-around
          $('#secretSauce').hide();
          $('#ultraSecret').hide();          
          displayMerch("type", "/all");
        });

        $('#userNav').on('click', '#loginButton', function() {
          $('#loginModal').modal('show');
        });

        $('#userNav').on('click', '#signUpButton', function() {
          $('#signUpModal').modal('show');
        });

        checkForLoginCookie();
        displayMerch("type", "/all");
          //$('#secretSauce').hide();
          //$('#ultraSecret').hide();          
    });





function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}
