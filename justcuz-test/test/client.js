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
      $('.main.menu  .ui.dropdown')
        .dropdown()
      ;
      
      function drawTable(data, value){
        if ((data.length != undefined) && (data.length != 0)) {
          var keys = Object.keys(data[0]);
          var cards = $("<div class= 'ui cards'>");
          
          for (var j = 0; j < data.length; j++) {
            cards.append($(drawCard(data[j],keys)));
          }
          cards.append($("</div>"));
          $('#catalogtest').html(cards);
        } else {
          drawNoResults();
        }

      }
    
      // labels is an array containing the attribute names
      function drawCard(cardData, labels) {
        var card = $("<a class='card' href='orderPage.html?inum=" + cardData.ITEM_NUM + "&points=" + points + "&cid="+ cid + "&memName=" + memName +"&email=" + emailad +"&addy=" + addy + "&cardNum=" + cardNum + "&cardType=" + cardType +"&price="+ cardData.PRICE  +"'>");
        card.append($("<div class='image'><img src='catalog/" + cardData.ITEM_NUM +".jpg'></div>"));
        card.append($("<div class='header'>" + cardData.ITEM_NUM + "</div>"));
        
        var desc = "";
        if(cardData.GENDER)
          desc += cardData.GENDER + " ";
        if(cardData.COLOR)
          desc += cardData.COLOR + " ";
        if(cardData.TYPE)
          desc += cardData.TYPE + " ";        
        if(cardData.COMPANY_NAME)
          desc += "from " + cardData.COMPANY_NAME;

        card.append($("<div class='content'><div class='meta'>$" + cardData.PRICE + "</div><div class='description'>" + desc + "</div></div></a>"));

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
                // if ( $('#price').is( ":checked" )) {
                //   price = "/price";
                //   console.log("show price");
                //   console.log(price);
                // }
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

        function checkForLoginCookie() {
            console.log('checking for cookie');
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

                  createCookie("justcuz", result["CID"], 1);
                  createCookie("justcuz-addr", result["EMAIL"], 1);

                  showLoginButton(false, result["NAME"]);//, result["CID"]);
                }

                if(userType == "mem") {
                  $('#secretSauce').hide();
                  $('#ultraSecret').hide();
                } else if(userType == "emp"){
                  $('#secretSauce').show();
                  $('#ultraSecret').hide();
                } else {
                  $('#secretSauce').show();
                  $('#ultraSecret').show();                  
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
          } else {
            $('#userArea').replaceWith("<button class='ui inverted basic button' id='loginButton'>Log in</button>");
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
                               
                addy = result["ADDRESS"];
                points = result["POINTS"];
                memName = result["NAME"];
                emailad = result["EMAIL"];
                userType = result["U_TYPE"];                
                cardNum = result["CARD_NUM"];
                cardType = result["CARD_TYPE"];

                createCookie("justcuz", cid, 1);
                createCookie("justcuz-addr", result["EMAIL"], 1);
   
                if(userType == "mem") {
                  $('#secretSauce').hide();
                  $('#ultraSecret').hide();
                } else if(userType == "emp"){
                  $('#secretSauce').show();
                  $('#ultraSecret').hide();
                } else {
                  $('#secretSauce').show();
                  $('#ultraSecret').show();                  
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

        $('#userNav').on('click', '#logoutButton', function(){
          points = 0;
          cid = undefined;


          //probably want a set function instead of changing values all over
          addy = undefined;
          memName = undefined;
          emailad = "";
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
