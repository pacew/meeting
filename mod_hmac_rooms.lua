local st = require "util.stanza";
local jid = require "util.jid";
local hashes = require "util.hashes";

local hmac_rooms_key = module:get_option("hmac_rooms_key", false);

local function fromhex(str)
    return (str:gsub('..', function (cc)
        return string.char(tonumber(cc, 16))
    end))
end

local function tohex(buf)
   ret = ""
   for i = 1,string.len(buf) do
      ret = ret .. string.format("%02x", buf:byte(i))
   end
   return ret
end


local function valid_room(room_name)
   if hmac_rooms_key == false then
      module:log("error", "hmac_rooms_key not set - no rooms allowed");
      return false
   end

   msg = string.lower(string.sub(room_name, 1, -9))
   sig_hex = string.sub(room_name, -8);

   computed = hashes.hmac_sha1(hmac_rooms_key, msg);
   computed = tohex(string.sub(computed, 1, 4))

   if computed ~= sig_hex then
      module:log("info", "hmac room rejected " .. room_name)
      return false
   end

   yyyy, mm, dd, duration = msg:match(".*(....)(..)(..)x(..)x")
   start = os.time{year=yyyy, month=mm, day=dd}
   delta = os.time() - start
   if delta > duration * 86400 then
      module:log("info", "hmac room expired " .. room_name)
      return false
   end

   return true
end

module:hook("presence/full", function(event)
        local stanza = event.stanza;

        if stanza.name == "presence" and stanza.attr.type == "unavailable" then
                return;
        end

	local room_name = jid.split(stanza.attr.from);
        if not room_name then return; end

	if valid_room(room_name) then
	   return
	end
	   
	event.allowed = false;
	event.stanza.attr.type = 'error';
	return event.origin.send(
	   st.error_reply(event.stanza, 
			  "cancel", 
			  "forbidden", 
			  "invalid room name"));
end, 10);

