# Event Manager

Example class that will also act as the event, would could create a separate event class to pass into the dispatch method.

	class Blog extends Events\Event {

		protected $events;

		public function __construct(EventManager $events) {
			$this->events = $events;
		}

		public function getTitle() {
			return 'My Blog';
		}

		public function update() {
			$this->events->dispatch('blog.update', $this);
		}

	}

Some plugin that will listen to some events.

	class SiteMapPlugin {

		protected $document;

		public function __construct(Docucment $document, EventManager $events) {
			$this->document = $document;
			$events->listen('blog.update', [$this, 'generate']);
		}

		public function generate(Blog $blog) {
			$this->document->setTitle($blog->getTitle());
			$this->document->save();
		}

	}

Putting it together

	$manager = new Events\EventManager();

	$sitemap = new SiteMapPlugin($manager);

	$blog = new Blog($manager);
	$blog->update();

same as:

	$sitemap->generate($blog);

except its called when the blog is updated.

We could also do it another way, removing the injection of the event manager:

	class SiteMapPlugin {
		...
		public function __construct(Docucment $document) {
			$this->document = $document;
		}
		...
	}

So we are left with:

	$manager = new Events\EventManager();
	$manager->listen('blog.update', [new SiteMapPlugin, 'generate']);

	$blog = new Blog($manager);
	$blog->update();
