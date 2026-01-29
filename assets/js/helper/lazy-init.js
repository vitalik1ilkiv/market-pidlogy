import $ from './jquery.js';

class LazyInit {
    constructor() {
        this.window         = $(window);
        this.body           = $('body');

        this.init();
    }

    init() {
        window.lazySizesConfig = window.lazySizesConfig || {};
        lazySizesConfig.loadMode = 1;

        // load video
        $('.lazy-video').one('lazybeforeunveil', function(){
            let video = $(this);
            video.attr(video.data());
        });

        // load script
        $('.lazy-script').one('lazybeforeunveil', function(){
            let block = $(this);
            $('<script src="'+ block.data('src') +'" async defer></'+'script>').insertBefore(block);
        });
    }

    onLazyload ($block, cb) {
        if ($($block).hasClass('lazyload')) {
            $($block).one('lazybeforeunveil', (e) => {
                if (!$($block).is(e.target)) return;
                cb($block);
            });
        } else {
            cb($block);
        }
    }
}

export default new LazyInit();
