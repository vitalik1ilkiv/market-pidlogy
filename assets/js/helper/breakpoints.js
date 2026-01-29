function getResize (mediaBreak) {
    let breakpoint;

    switch (mediaBreak) {
        case "XS": breakpoint = window.matchMedia('(max-width: 375px)'); break;
        case "SM": breakpoint = window.matchMedia('(min-width: 375px)'); break;
        case "MD-1": breakpoint = window.matchMedia('(max-width: 767px)'); break;
        case "MD": breakpoint = window.matchMedia('(min-width: 768px)'); break;
        case "LG-1": breakpoint = window.matchMedia('(max-width: 1023px)'); break;
        case "LG": breakpoint = window.matchMedia('(min-width: 1024px)'); break;
        case "XL-1": breakpoint = window.matchMedia('(max-width: 1279px)'); break;
        case "XL": breakpoint = window.matchMedia('(min-width: 1280px)'); break;
        case "XLL": breakpoint = window.matchMedia('(min-width: 1600px)'); break;

        default: breakpoint = window.matchMedia('all'); break;
    }

    return breakpoint;
}
export {getResize};
