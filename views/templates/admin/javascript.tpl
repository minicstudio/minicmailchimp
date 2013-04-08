<script type="text/javascript">
jQuery(window).load(function(){
    {if $minic.first_start}
    // First start
    $('#newsletter').fadeIn();
    minic.newsletter(false);
    {/if}
});
jQuery(document).ready(function($) {
    // News Feed
    $.getJSON('http://clients.minic.ro/process/feed?callback=?',function(feed){
        var version = '{$minic.info.version}';
        var name = '{$minic.info.module}';
        
        // Banner
        if(typeof(feed['modules'][name]) != "undefined" && feed['modules'][name]['version'] != version){
            $('#banner').empty().html(feed['modules'][name]['update']);
        }else if(typeof(feed['modules'][name]) != "undefined" && feed['modules'][name]['news']){
            $('#banner').empty().html(feed['modules'][name]['news']);
        }else{
            $('#banner').empty().html(feed['news']);
        }

        // Module list
        if(feed.modules){
            list = '';
            $.each(feed.modules, function() {
                
                list += '<li>';
                list += '<a href="'+ this.link +'" target="_blank" title="{l s='Click for more details' mod='minicskepetonpro'}">';
                list += '<img src="'+ this.logo +'">';
                list += '<p>';
                list += '<span class="title">'+ this.name +'</span>';
                list += '<span class="description">'+ this.description +'</span>';
                list += '<span class="price">'+ this.price +'</span>';
                list += '</p>';
                list += '</a>';
                list += '</li>';
            });
            
        }
        $('ul#module-list').html(list);
        
    });
});
</script>