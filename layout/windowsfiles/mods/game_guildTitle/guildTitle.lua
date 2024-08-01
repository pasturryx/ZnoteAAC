local titleFont = "verdana-11px-rounded"
local OPCODE = 134

function init()
  print("init() called")
  connect(Creature, {
    onAppear = requestData,
  })  
  ProtocolGame.registerExtendedOpcode(OPCODE, handleData)
end

function terminate()
  print("terminate() called")
  disconnect(Creature, {
    onAppear = requestData,
  })  
  ProtocolGame.unregisterExtendedOpcode(OPCODE, handleData)
end

function requestData(creature)
  print("requestData() called for creature:", creature:getName())
  if creature:isPlayer() then
    local tbl = {
      creature = creature:getId()
    }
    local protocolGame = g_game.getProtocolGame()
    if protocolGame then
      print("Sending extended opcode for creature:", creature:getName())
      protocolGame.sendExtendedOpcode(protocolGame, OPCODE, json.encode(tbl))
    end
  end
end

function handleData(protocol, code, buffer)
  print("handleData() called")
  local json_status, json_data = pcall(function()
    return json.decode(buffer)
  end)

  if not json_status then
    g_logger.error("JSON error: " .. json_data)
    return false
  end

  local data = json_data
  local response = json_data.response
  print("Received response:", response) -- Debugging line to check the response

  if data ~= nil then
    if response == "setGuildNick" then
      print("Updating title for creature with setGuildNick response")
      print("Guild Nick:", data.guildNick)
      print("Player is in a guild:", data.guildName) -- Adding this line to print the guild name
      updateTitle(data.creatureId, data.guildNick)
    elseif response == "setGuildName" then
      print("Updating title for creature with setGuildName response")
      print("Guild Name:", data.guildName)
      print("Player is in a guild:", data.guildName) -- Adding this line to print the guild name
      updateTitle(data.creatureId, data.guildName)
    else
      print("Unknown response:", response)
    end
  else
    print("No data received.")
  end

  return
end

--function updateTitle(creatureId, title)
  --print("updateTitle() called for creature ID:", creatureId, "and title:", title)
  --local target = g_map.getCreatureById(creatureId)
  --if target then
    --target:setTitle(title, titleFont, "#00e378")
  --end
  --return
--end
local guildColors = {}

function updateTitle(creatureId, guildName)
  print("updateTitle() called for creature ID:", creatureId, "and title:", title)
    local target = g_map.getCreatureById(creatureId)
    if target then
        local color
        if guildColors[guildName] then
            color = guildColors[guildName]
        else
            local red = math.random(0, 255)
            local green = math.random(0, 255)
            local blue = math.random(0, 255)
            color = string.format("#%02X%02X%02X", red, green, blue)
            guildColors[guildName] = color
        end
        target:setTitle(guildName, titleFont, color)
    end
end