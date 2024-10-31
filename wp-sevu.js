// Disable "Discourage search engines from indexing this site" option on the admin panel
jQuery( document ).ready(function($) {
  $("input#blog_public").attr("disabled", true);
});
