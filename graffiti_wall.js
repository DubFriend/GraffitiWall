(function () {
    "use strict";

    var GRAFFITI_WALL_URL = "example.php", //change to match the filepath of the script that handles graffiti_wall.php
        TWOPI = Math.PI * 2,
        Context = $('#canvas')[0].getContext("2d"),
        Canvas = $('#canvas'),
        paint = false,
        storedColor,
        storedSize,

        Moves = (function () {

            var AllMoves = [],
                index = 0,
                paintCircle = function (Move) {
                    storedColor = Move.c || storedColor;
                    storedSize = Move.s || storedSize;

                    Context.fillStyle = '#' + storedColor;
                    Context.beginPath();
                    Context.arc(Move.x, Move.y, storedSize, 0, TWOPI, true);
                    Context.closePath();
                    Context.fill();
                };

            return {
                renderMoveSet: function (Moves) {
                    Canvas.width = Canvas.width;
                    var i;
                    for (i = 0; i < Moves.length; i += 1) {
                        paintCircle(Moves[i]);
                    }
                },
                add: function (Move) {
                    if(Move.c === storedColor) {
                        delete Move.c;
                    }
                    if(Move.s === storedSize) {
                        delete Move.s;
                    }
                    Move.x = Math.round(Move.x);
                    Move.y = Math.round(Move.y);
                    AllMoves.push(Move);
                },
                render: function () {
                    Canvas.width = Canvas.width; // Clears the canvas
                    var i;
                    for (i = 0; i < AllMoves.length; i += 1) {
                        paintCircle(AllMoves[i]);
                    }
                },
                toJSON: function () {
                    return JSON.stringify(AllMoves);
                }
            };
        }()),

        newMove = function (coord, color, size) {
            return {
                x: coord.x,
                y: coord.y,
                c: color,
                s: size
            };
        },

        getMouseCoord = function (e) {
            var coordinates = Canvas.offset();
            return {
                "x": e.pageX - coordinates.left,
                "y": e.pageY - coordinates.top
            };
        },

        getSelectedColor = function () {
            var selected  = $('#paint_color input');
            return selected.val();
        },

        getSelectedSize = function () {
            return $("#brush_size_slider").slider("value");
        };


    //paint the old stuff.
    (function () {
        var moves = $.parseJSON($('#old_moves_data').html());
        Moves.renderMoveSet(moves);
    }());


    $("#brush_size_slider").slider({
        animate: "fast",
        max: 50,
        min: 1,
        step: 1,
        value: 5
    });

    Canvas.mousedown(function (e) {
        paint = true;
        Moves.add(newMove(getMouseCoord(e), getSelectedColor(), getSelectedSize()));
        Moves.render();
    });

    Canvas.mousemove(function (e) {
        if (paint) {
            Moves.add(newMove(getMouseCoord(e), getSelectedColor(), getSelectedSize()));
            Moves.render();
        }
    });

    Canvas.mouseup(function (e) {
        paint = false;
    });

    Canvas.mouseleave(function (e) {
        paint = false;
    });

    var bind_form_submit = function(spec) {
        
        var validate = spec.validate || function () { return true; };

        spec.FormRef.submit(function(e) {
            if(validate()) {
                if(spec.before) {
                    //jquery's beforeSend, apparently happens after dom POST variables are processed.
                    spec.before();
                }
                send_ajax_request({
                    url: spec.url,
                    data: $(e.target).serialize(),
                    dataType: spec.dataType || 'json',
                    beforeSend: spec.beforeSend || function () {},
                    success: spec.response || function (json) {}
                });
            }
            return false; //return false to block form submit.
        });
    };

    var send_ajax_request = function(spec) {
        var type = spec['type'] || 'POST',
            dataType = spec.dataType || 'json',
            errorFunction = spec['error'] || function (jqXHR, textStatus, errorThrown) {},
            success = spec.success || function (json) {},
            beforeSend = spec.beforeSend || function () {};

        $.ajax({
            type: type,
            url: spec.url,
            dataType: dataType,
            data: spec['data'],
            error: errorFunction,
            beforeSend: beforeSend,
            success: success
        });
    }

    bind_form_submit({
        FormRef: $('#save_painting'),
        url: GRAFFITI_WALL_URL + "?act=save_painting",
        dataType: "text",
        before: function () {
            $('#paint_moves').val(Moves.toJSON());
        },
        response: function (text) {
            location.href = GRAFFITI_WALL_URL;
        }
    });

}());