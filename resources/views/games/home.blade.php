@extends('layouts.wrapper')

@section('title')
<title>All Games | TwitchRandom</title>
@stop

@section('meta')
<meta name="description" content="TwitchRandom.com - All Live Games. Find something unexpected at https://twitchrandom.com!">
@stop

@section('css')
@stop

@section('js')
<script src="/js/typeahead.bundle.min.js"></script>
<script>
    @include("layouts.js.loading")

    $(document).ready(function(){
        //$("html").niceScroll({cursorcolor:"#6441A5"});
        var offset = 0;
        $.ajax({
            url: "/ajax/games/50/0"
        }).done(function(data){
            $("#games-loading").hide();
            $(".games_container").append(data);
            offset=offset+50;
        }).fail(function(data){
            console.log(data);
            $(".jumbotron>.loading>.text").addClass("error").text("Error: "+data.responseJSON.error.message);
        });

        var engine = new Bloodhound({
            name: 'games',
            //local: [{name: 'game', link: '/games/Team Fortress 2'}, {name: 'game2', link: '/games/League of Legends'}],
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote:{
                url: "/ajax/search/%QUERY",
                wildcard: '%QUERY',
                filter: function (response) {
                    return $.map(response, function(value,key){
                       return {
                           name: key,
                           link: value
                       }
                    });
                },
                ajax: {
                    beforeSend: function(){
                        if($("#search-loading").length == 0){
                            $(".twitter-typeahead").append('<div class="loading" id="search-loading"><img src="/img/loading.gif" alt="loading"></div>')
                        }else{
                            $("#search-loading").show();
                        }
                    },
                    complete: function(){
                        $("#search-loading").hide();
                    }
                }
            },
            /*datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace*/
        });
        engine.initialize();

        $("#game_search").typeahead({
            minLength:1,
            hint:false,
            highlight:false
        },{
            name: 'games',
            displayKey: 'name',
            source: engine,
            templates: {
                empty: '<div class="empty-message">No Games Found</div>',
                //suggestion: Handlebars.compile('<p><a href="link">name</a></p>')
                suggestion: function(data){
                    return '<p><a href="'+data.link+'">'+data.name+'</a></p>';
                }
            }
        });

        $("#load_more").click(function(){
           $(this).hide();
            $("#games-loading").show();
            $.ajax({
                url: ("/ajax/games/50/"+offset)
            }).done(function(data){
                $("#games-loading").hide();
                $(".games_container").append(data);
                $("#load_more").show();
                offset=offset+50;
            }).fail(function(data){
                console.log(data);
                $(".jumbotron>.loading>.text").addClass("error").text("Error: "+data.responseJSON.error.message);
            });
        });

        //setInterval(function(){ $(".loading:visible>.text").setRandomText(); }, 1600);
    });
</script>
@stop

@section('content')

@include("layouts.header")
<div class="container">
    @include("layouts.ads.horizontal")
    <div class="row">
        <div class="col-sm-12">
            <div class="all_games">
                <div class="fixed_header">
                    <div class="search-container">
                        <input id="game_search" type="text" class="form-control" placeholder="Search for a game" autocomplete="off" spellcheck="false">
                    </div>
                    <h1>All Games</h1>
                </div>
                <div class="games_container row">
                </div>
                <div class="loading" id="games-loading">
                    <img src="/img/loading.gif" alt="loading">
                    <span class="text">Loading Games...</span>
                </div>
                <div class="load_more_cont">
                    <button class="btn btn-lg btn-twitch" id="load_more">Load More Games</button>
                </div>
            </div>
        </div>
    </div>
</div>
@include("layouts.ads.horizontal2")
@include("layouts.footer")
@stop