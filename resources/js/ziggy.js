const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"login":{"uri":"login","methods":["GET","HEAD"]},"login.post":{"uri":"login","methods":["POST"]},"logout":{"uri":"logout","methods":["POST"]},"home":{"uri":"\/","methods":["GET","HEAD"]},"new-trip":{"uri":"new-trip","methods":["GET","HEAD"]},"new-trip.store":{"uri":"new-trip","methods":["POST"]},"map":{"uri":"map","methods":["GET","HEAD"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
