const requestAnimationFrame = window.requestAnimationFrame || setTimeout;

let rAF = (function () {
  let running,
    waiting,
    firstFns = [],
    secondFns = [],
    fns = firstFns;

  const run = function () {
    const runFns = fns;

    fns = firstFns.length ? secondFns : firstFns;

    running = true;
    waiting = false;

    while (runFns.length) {
      runFns.shift()();
    }

    running = false;
  };

  let rafBatch = function (fn, queue) {
    if (running && !queue) {
      fn.apply(this, arguments);
    } else {
      fns.push(fn);

      if (!waiting) {
        waiting = true;
        (document.hidden ? setTimeout : requestAnimationFrame)(run);
      }
    }
  };

  rafBatch._lsFlush = run;

  return rafBatch;
})();

export default function raf(fn, simple) {
  return simple
    ? function () {
        rAF(fn);
      }
    : function () {
        let that = this,
          args = arguments;
        rAF(function () {
          fn.apply(that, args);
        });
      };
}
