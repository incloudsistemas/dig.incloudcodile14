class WebCustom {
    constructor() {
        this.videoplayOnHover();
    }

    videoplayOnHover() {
        var videoElements = document.querySelectorAll('.videoplay-on-hover');

        videoElements.forEach(function (videoElement) {
            videoElement.addEventListener('mouseover', function () {
                var video = videoElement.querySelector('video');
                if (video) {
                    video.play();
                }
            });

            videoElement.addEventListener('mouseout', function () {
                var video = videoElement.querySelector('video');
                if (video) {
                    video.pause();
                }
            });
        });
    }
}

const webCustom = new WebCustom();
