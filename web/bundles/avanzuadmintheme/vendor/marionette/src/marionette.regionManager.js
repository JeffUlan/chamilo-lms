// Marionette.RegionManager
// ------------------------
//
// Manage one or more related `Marionette.Region` objects.
Marionette.RegionManager = (function(Marionette) {

  var RegionManager = Marionette.Controller.extend({
    constructor: function(options) {
      this._regions = {};
      Marionette.Controller.call(this, options);
    },

    // Add multiple regions using an object literal, where
    // each key becomes the region name, and each value is
    // the region definition.
    addRegions: function(regionDefinitions, defaults) {
      var regions = {};

      _.each(regionDefinitions, function(definition, name) {
        if (_.isString(definition)) {
          definition = {selector: definition};
        }

        if (definition.selector) {
          definition = _.defaults({}, definition, defaults);
        }

        var region = this.addRegion(name, definition);
        regions[name] = region;
      }, this);

      return regions;
    },

    // Add an individual region to the region manager,
    // and return the region instance
    addRegion: function(name, definition) {
      var region;

      var isObject = _.isObject(definition);
      var isString = _.isString(definition);
      var hasSelector = !!definition.selector;

      if (isString || (isObject && hasSelector)) {
        region = Marionette.Region.buildRegion(definition, Marionette.Region);
      } else if (_.isFunction(definition)) {
        region = Marionette.Region.buildRegion(definition, Marionette.Region);
      } else {
        region = definition;
      }

      this.triggerMethod('before:add:region', name, region);

      this._store(name, region);

      this.triggerMethod('add:region', name, region);
      return region;
    },

    // Get a region by name
    get: function(name) {
      return this._regions[name];
    },

    // Gets all the regions contained within
    // the `regionManager` instance.
    getRegions: function(){
      return _.clone(this._regions);
    },

    // Remove a region by name
    removeRegion: function(name) {
      var region = this._regions[name];
      this._remove(name, region);
    },

    // Empty all regions in the region manager, and
    // remove them
    removeRegions: function() {
      _.each(this._regions, function(region, name) {
        this._remove(name, region);
      }, this);
    },

    // Empty all regions in the region manager, but
    // leave them attached
    emptyRegions: function() {
      _.each(this._regions, function(region) {
        region.empty();
      }, this);
    },

    // Destroy all regions and shut down the region
    // manager entirely
    destroy: function() {
      this.removeRegions();
      Marionette.Controller.prototype.destroy.apply(this, arguments);
    },

    // internal method to store regions
    _store: function(name, region) {
      this._regions[name] = region;
      this._setLength();
    },

    // internal method to remove a region
    _remove: function(name, region) {
      this.triggerMethod('before:remove:region', name, region);
      region.empty();
      region.stopListening();
      delete this._regions[name];
      this._setLength();
      this.triggerMethod('remove:region', name, region);
    },

    // set the number of regions current held
    _setLength: function() {
      this.length = _.size(this._regions);
    }

  });

  Marionette.actAsCollection(RegionManager.prototype, '_regions');

  return RegionManager;
})(Marionette);
