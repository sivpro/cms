					jQuery(document).ready(function(){
						
						/*feedback form*/
						jQuery("#showFormOs, .openform").click(function() {
							jQuery(".overlay").remove();
							jQuery(".cont-div").remove();
							var div = jQuery("<div class='overlay'></div>");
							var contdiv = jQuery("<div class='cont-div'></div>");

							div.css("display", "none");
							contdiv.css("display", "none");
							
							contdiv.appendTo("body").show();
							div.appendTo("body").show();
							jQuery("#form").hide().appendTo(contdiv).show("slow");

							var wh = $(window).height();
							var fh = $("#form").height()+100;

							
							div.height(jQuery(document.body).height());							
							if (wh < 700) {
								contdiv.css({
									position: "absolute",
									top: "20px"
								});
								window.scroll(0, 20);
							}

							
							
							jQuery(".overlay, .close").click(function(e) {
								jQuery("#form").appendTo("#form-there");
								div.remove();
								contdiv.remove();								
								return false;
							});
							return false;
							
						});
						
						
					});
