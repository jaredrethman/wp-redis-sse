(function () {
  console.log("loaded");
  const listeners = {
    message: undefined,
    open(e) {
      console.log("SSE opened!", e);
    },
    error(e) {
      if (e.readyState == EventSource.CLOSED) {
        console.log("SSE closed!");
      }
    },
    subscribe(e) {
      console.log("SSE subscribed:", e);
    },
    wpRedisSse({ data }) {
      const update = JSON.parse(data);
      console.log("Site option updated to:", update, data);
    },
    default(e) {
      console.log("Default:", e);
    },
  };

  const serverSentEvent = {
    source: undefined,
    connect() {
      // @TODO pass credentials along with this script
      this.source = new EventSource(`${window.wpRedisSse.eventSource}`);
      return this;
    },
    disconnect() {
      this.source.close();
      return this;
    },
		/**
		 * Helper for adding or removing event listeners
		 * @param {string} action String either `'add'` or `'remove'`
		 * @returns 
		 */
    eventListeners(action = "add") {
      for (const [eventName, eventListener] of Object.entries(listeners)) {
        this.source[`${action}EventListener`](
          eventName,
          eventListener ?? listeners.default
        );
      }
      return this;
    },
  };

  // Start
  serverSentEvent.connect().eventListeners();
})();
