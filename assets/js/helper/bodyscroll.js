import $ from './jquery.js';

class GetBodyScroll {
    constructor() {
        this.body      = $('body');
        this.is_safari = /(Mac)/i.test(navigator.platform);
        this.indent    = 0;
    }

    disable() {
        if(!this.body.is('.disable-scroll')) {
            this.indent = $(window).scrollTop();
            this.body.addClass('disable-scroll').find('.wp-site-blocks').css('top', `-${this.indent}px`);
        }

        // if (!this.is_safari) {
        //     if(!this.body.is('.disable-scroll')) {
        //         this.indent = $(window).scrollTop();
        //         this.body.addClass('disable-scroll').find('.wp-site-blocks').css('top', `-${this.indent}px`);
        //     }
        // } else {
        //     this.body.addClass('disable-scroll safari-scroll');
        // }
    }

    enable() {
        if (this.body.is('.disable-scroll')) {
            this.body.removeClass('disable-scroll').find('.wp-site-blocks').removeAttr('style');
            $('body,html').scrollTop(this.indent);
        }

        // if (!this.is_safari) {
        //     if (this.body.is('.disable-scroll')) {
        //         this.body.removeClass('disable-scroll').find('.wp-site-blocks').removeAttr('style');
        //         $('body,html').scrollTop(this.indent);
        //     }
        // } else {
        //     this.body.removeClass('disable-scroll safari-scroll');
        // }
    }
}

export default new GetBodyScroll();
