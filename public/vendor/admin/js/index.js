(function(app){
app.module('index',function(){
    var mod={};
    mod.boot=function(){
        console.log('boot index');
    };
    return mod;
})})(la);