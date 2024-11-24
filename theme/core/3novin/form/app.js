$(function(){

	// Create a model for the Books
	var Book = Backbone.Model.extend({

		// Will contain three attributes.
		// These are their default values

		defaults:{
			title: 'کتاب',
			price: 1000,
			checked: false,
			name: 'book0'
		},

		// Helper function for checking/unchecking a Book
		toggle: function(){
			this.set('checked', !this.get('checked'));
		}
	});


	// Create a collection of Books
	var BookList = Backbone.Collection.extend({

		// Will hold objects of the Book model
		model: Book,

		// Return an array only with the checked Books
		getChecked: function(){
			return this.where({checked:true});
		}
	});

	// Prefill the collection with a number of Books.
	var Books = new BookList([
		new Book({ title: 'سیستم مدیریت محتوی نوین رسانه', price: 300, name: 'book1'}),
		new Book({ title: 'ماژول فرم ساز', price: 80, name: 'book2'}),
		new Book({ title: 'ماژول گفتگو آنلاین', price: 70, name: 'book3'}),
		new Book({ title: 'ماژول منو ساز', price: 50, name: 'book4'}),
		new Book({ title: 'ماژول مدیریت اسلایدر', price: 50, name: 'book5'}),
		new Book({ title: 'ماژول نقشه سایت', price: 20, name: 'book6'}),
		new Book({ title: 'ماژول تیتر خبر', price: 20, name: 'book7'}),
		new Book({ title: 'ماژول گالری تصاویر', price: 60, name: 'book8'}),
		new Book({ title: 'ماژول تبادل لینک هوشمند', price: 60, name: 'book9'}),
		new Book({ title: 'ماژول مدیریت ایمیل', price: 100, name: 'book10'}),
		new Book({ title: 'ماژول خبر نامه', price: 50, name: 'book11'}),
		new Book({ title: 'ماژول خروجی PDF مطالب', price: 20, name: 'book12'}),
		new Book({ title: 'ماژول نظرسنجی', price: 40, name: 'book13'}),
		new Book({ title: 'ماژول خبرخوان هوشمند', price: 50, name: 'book14'}),
		new Book({ title: 'ماژول ارسال مطلب توسط كاربران', price: 50, name: 'book15'}),
		new Book({ title: 'ماژول مدیریت لینک های شبکه های اجتماعی', price: 20, name: 'book16'}),
		new Book({ title: 'ماژول آب و هوا', price: 30, name: 'book17'})
		// Add more here
	]);

	// This view turns a Book model into HTML
	var BookView = Backbone.View.extend({
		tagName: 'li',

		events:{
			'click': 'toggleBook'
		},

		initialize: function(){

			// Set up event listeners. The change backbone event
			// is raised when a property changes (like the checked field)

			this.listenTo(this.model, 'change', this.render);
		},

		render: function(){

			// Create the HTML

			this.$el.html('<input type="checkbox" value="1" name="' + this.model.get('title') + '" /> ' + this.model.get('title') + '<span>' + this.model.get('price') + ' تومان</span>');
			this.$('input').prop('checked', this.model.get('checked'));

			// Returning the object is a good practice
			// that makes chaining possible
			return this;
		},

		toggleBook: function(){
			this.model.toggle();
		}
	});

	// The main view of the application
	var App = Backbone.View.extend({

		// Base the view on an existing element
		el: $('#main'),

		initialize: function(){

			// Cache these selectors
			this.total = $('#total span');
			this.list = $('#books');
			
			// Listen for the change event on the collection.
			// This is equivalent to listening on every one of the 
			// Book objects in the collection.
			this.listenTo(Books, 'change', this.render);

			
			// Create views for every one of the Books in the
			// collection and add them to the page

			Books.each(function(Book){

				var view = new BookView({ model: Book });
				this.list.append(view.render().el);

			}, this);	// "this" is the context in the callback
		},

		render: function(){

			// Calculate the total order amount by agregating
			// the prices of only the checked elements

			var total = 0;

			_.each(Books.getChecked(), function(elem){
				total += elem.get('price');
			});

			// Update the total price
			
			this.total.text(total + ' تومان');

			return this;

		}

	});

	new App();

});