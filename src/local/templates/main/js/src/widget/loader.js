(function(App) {

	window.App = App = App || {};

	var Loader = App.LoaderWidget = App.Widget.extend({
		defaults: function(){
			return {
				imgSrc: '/local/templates/main/images/loader.svg',
				imgWidth: 96,
				imgHeight: 96,
				bgColor: 'rgba(255, 255, 255, 0)'
			}
		},

		initialize: function(){
		    if(!this.$el.length){
		        return;
            }
			this.__initBackground();
		},

		__initBackground: function(){
			var offset = this.$el.offset();
			var width = this.$el.outerWidth();
			var height = this.$el.outerHeight();
			this.$bg = $('<div class="loaderwidget">', {
				css: {
					display:'none',
					position: 'absolute',
					top: offset.top,
					left: offset.left,
					width: width + 'px',
					height: height + 'px',
					opacity: this.bgOpacity,
					backgroundColor: this.bgColor,
					textAlign: 'center',
					margin: 'auto'
				}
			});
			var $img = this.__createImage(width, height);
			this.$bg.append($img);
			this.$el.after(this.$bg);
		},

		__createImage: function(width, height){
			return $('<img>', {
				src: this.imgSrc,
				width: this.imgWidth,
				height: this.imgHeight,
				css: {
					//marginLeft: ((width - this.imgWidth)/2) + 'px',
					marginTop: ((height - this.imgHeight)/2) + 'px'
				}
			});
		},

		show: function(){
		    if(!this.$el.length){
		        return;
            }
			if(!this.$bg){
				this.__initBackground();
			}
			this.$bg.show();
		},

		hide: function(){
			this.$bg.hide();
		},

		reset: function(){
			this.$bg.remove();
			delete this.$bg;
		}

	});

})(window.App);