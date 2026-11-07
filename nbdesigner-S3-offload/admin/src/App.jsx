import React, { useState, useEffect } from 'react';
import {
  Cloud,
  HardDrive,
  ShieldCheck,
  Type,
  BarChart3,
  RefreshCw,
  Download,
  UploadCloud,
  CheckCircle2,
  AlertCircle,
  Folder,
  Layers,
  Database,
  ArrowRight,
  Sparkles,
  Search,
  Filter,
  Eye,
  Check,
  Zap,
  ChevronLeft,
  ChevronRight,
  Play,
  Pause,
  Clock,
  Activity,
  Trash2,
  Wifi,
  WifiOff,
  ScrollText,
  Radio,
  Sliders,
  Flame,
  Archive,
  ExternalLink,
} from 'lucide-react';

const PRESETS = {
  wasabi: {
    name: 'Wasabi Cloud Storage',
    region: 'us-east-1',
    endpoint: 's3.wasabisys.com',
    use_path_style: true,
  },
  backblaze: {
    name: 'Backblaze B2 (S3 Compatible)',
    region: 'us-east-005',
    endpoint: 's3.us-east-005.backblazeb2.com',
    use_path_style: true,
  },
  bunny: {
    name: 'Bunny Storage / BunnyCDN',
    region: 'de',
    endpoint: 'storage.bunnycdn.com',
    use_path_style: true,
  },
  digitalocean: {
    name: 'DigitalOcean Spaces',
    region: 'nyc3',
    endpoint: 'nyc3.digitaloceanspaces.com',
    use_path_style: true,
  },
  aws: {
    name: 'Amazon Web Services S3',
    region: 'us-east-1',
    endpoint: 's3.amazonaws.com',
    use_path_style: false,
  },
  custom: {
    name: 'Custom S3 Compatible Storage',
    region: 'us-east-1',
    endpoint: '',
    use_path_style: true,
  },
};

function ToggleSwitch({
  checked,
  onChange,
  disabled = false,
  activeColor = 'bg-indigo-600',
  size = 'md',
  ariaLabel = 'Toggle setting',
}) {
  const isSm = size === 'sm';
  const width = isSm ? 'w-9' : 'w-11';
  const height = isSm ? 'h-5' : 'h-6';
  const thumbSize = isSm ? 'h-3.5 w-3.5' : 'h-4.5 w-4.5';
  const translate = isSm
    ? (checked ? 'translate-x-4' : 'translate-x-0.5')
    : (checked ? 'translate-x-5.5' : 'translate-x-0.5');

  return (
    <button
      type="button"
      role="switch"
      aria-label={ariaLabel}
      aria-checked={checked}
      disabled={disabled}
      onClick={() => !disabled && onChange(!checked)}
      className={`relative inline-flex items-center ${height} ${width} flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed ${
        checked ? activeColor : 'bg-slate-300'
      }`}
    >
      <span
        className={`pointer-events-none inline-block ${thumbSize} transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out ${translate}`}
      />
    </button>
  );
}

export default function App() {
  const [activeTab, setActiveTab] = useState('explorer');
  const [settings, setSettings] = useState({
    provider: 'wasabi',
    access_key: '',
    secret_key: '',
    region: 'us-east-1',
    bucket: '',
    endpoint: 's3.wasabisys.com',
    use_path_style: true,
    cdn_url: '',
    enable_font_offload: true,
    enable_cold_order_offload: true,
    enable_template_offload: false,
    enable_saved_design_offload: false,
    cold_retention_days: 14,
    purge_mode: 'smart',
    cleanup_abandoned_drafts: true,
    abandoned_drafts_days: 7,
    font_cache_max_age: 2592000,
    sync_paused: true,
    sync_schedule_time: '03:00',
    enable_activity_log: true,
    max_log_entries: 200,
  });

  const [stats, setStats] = useState(null);
  const [syncStatus, setSyncStatus] = useState({
    is_running: false,
    is_paused: true,
    current_action: 'Idle',
    started_at: null,
    server_time: '',
    server_timezone: '',
    sync_schedule_time: '03:00',
    next_scheduled_run: null,
  });
  const [bucketConnection, setBucketConnection] = useState({
    connected: false,
    message: '',
    bucket: '',
    latency_ms: 0,
    tested_at: null,
  });
  const [checkingConnection, setCheckingConnection] = useState(false);
  const [togglingPause, setTogglingPause] = useState(false);

  // Activity Log state
  const [logs, setLogs] = useState([]);
  const [logsLoading, setLogsLoading] = useState(false);
  const [clearingLogs, setClearingLogs] = useState(false);
  const [logsFilter, setLogsFilter] = useState('all');

  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [testing, setTesting] = useState(false);
  const [syncingFonts, setSyncingFonts] = useState(false);
  const [sweeping, setSweeping] = useState(false);
  const [restoringTemplates, setRestoringTemplates] = useState(false);
  const [notice, setNotice] = useState(null);

  // Sandbox Explorer state
  const [folders, setFolders] = useState([]);
  const [foldersTotal, setFoldersTotal] = useState(0);
  const [explorerFilter, setExplorerFilter] = useState('all');
  const [explorerLoading, setExplorerLoading] = useState(false);
  const [explorerError, setExplorerError] = useState(null);
  const [explorerSearch, setExplorerSearch] = useState('');
  const [explorerPage, setExplorerPage] = useState(0);
  const PAGE_SIZE = 25;
  const [operatingFolder, setOperatingFolder] = useState(null);
  const [batchCount, setBatchCount] = useState(3);
  const [batchRunning, setBatchRunning] = useState(false);

  const config = window.nbdS3OffloadConfig || {
    restUrl: '/wp-json/nbd-s3-offload/v1',
    nonce: '',
  };

  const getHeaders = () => ({
    'Content-Type': 'application/json',
    'X-WP-Nonce': config.nonce,
  });

  useEffect(() => {
    fetchSettings();
    fetchStats();
    fetchConnectionStatus(false);
    fetchLogs();
    fetchExplorerFolders('all', false, 0, '');

    // Poll live status every 15s to monitor active sync progress
    const interval = setInterval(() => {
      fetchStats();
    }, 15000);
    return () => clearInterval(interval);
  }, []);

  const showNotice = (type, message) => {
    setNotice({ type, message });
    setTimeout(() => setNotice(null), 6000);
  };

  const fetchSettings = async () => {
    try {
      const res = await fetch(`${config.restUrl}/settings`, { headers: getHeaders() });
      if (res.ok) {
        const data = await res.json();
        if (data.settings) {
          setSettings((prev) => ({ ...prev, ...data.settings }));
        }
      }
    } catch (err) {
      console.error('Failed to load settings:', err);
    }
  };

  const fetchStats = async () => {
    setLoading(true);
    try {
      const res = await fetch(`${config.restUrl}/stats`, { headers: getHeaders() });
      if (res.ok) {
        const data = await res.json();
        setStats(data.stats || data);
        if (data.sync_status) {
          setSyncStatus(data.sync_status);
        }
        if (data.bucket_connection) {
          setBucketConnection(data.bucket_connection);
        }
      }
    } catch (err) {
      console.error('Failed to load stats:', err);
    } finally {
      setLoading(false);
    }
  };

  const fetchConnectionStatus = async (force = true) => {
    setCheckingConnection(true);
    try {
      const res = await fetch(`${config.restUrl}/connection-status${force ? '?force=1' : ''}`, { headers: getHeaders() });
      if (res.ok) {
        const data = await res.json();
        if (data.status) {
          setBucketConnection(data.status);
          if (force) {
            if (data.status.connected) {
              showNotice('success', data.status.message || 'Connected to S3 bucket!');
            } else {
              showNotice('error', data.status.message || 'Failed to connect to S3 bucket.');
            }
          }
        }
      }
    } catch (err) {
      console.error('Failed to probe connection:', err);
      if (force) {
        showNotice('error', 'Connection probe failed: ' + err.message);
      }
    } finally {
      setCheckingConnection(false);
    }
  };

  const handleTogglePause = async () => {
    const targetState = !syncStatus.is_paused;
    const confirmMsg = targetState
      ? 'Pause all automated S3 uploads and local file deletions? Safe Mode will prevent any background sync.'
      : 'Resume automated S3 uploads and cold order offloads according to your configured schedule?';
    if (!window.confirm(confirmMsg)) {
      return;
    }
    setTogglingPause(true);
    try {
      const res = await fetch(`${config.restUrl}/sync/toggle-pause`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({ paused: targetState }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setSyncStatus(data.sync_status || ((prev) => ({ ...prev, is_paused: targetState })));
        setSettings((prev) => ({ ...prev, sync_paused: targetState }));
        showNotice(targetState ? 'warning' : 'success', data.message);
        fetchStats();
        fetchLogs();
      } else {
        showNotice('error', data.message || 'Failed to toggle sync pause state.');
      }
    } catch (err) {
      showNotice('error', 'Network error toggling sync pause: ' + err.message);
    } finally {
      setTogglingPause(false);
    }
  };

  const fetchLogs = async () => {
    setLogsLoading(true);
    try {
      const res = await fetch(`${config.restUrl}/logs?limit=150`, { headers: getHeaders() });
      if (res.ok) {
        const data = await res.json();
        setLogs(data.logs || []);
      }
    } catch (err) {
      console.error('Failed to load logs:', err);
    } finally {
      setLogsLoading(false);
    }
  };

  const handleClearLogs = async () => {
    if (!window.confirm('Clear all activity audit logs from the database?')) {
      return;
    }
    setClearingLogs(true);
    try {
      const res = await fetch(`${config.restUrl}/logs/clear`, {
        method: 'POST',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        setLogs([]);
        showNotice('success', 'Activity log cleared.');
      } else {
        showNotice('error', data.message || 'Failed to clear activity log.');
      }
    } catch (err) {
      showNotice('error', 'Network error clearing logs: ' + err.message);
    } finally {
      setClearingLogs(false);
    }
  };

  const fetchExplorerFolders = async (
    filter = explorerFilter,
    forceRefresh = false,
    page = explorerPage,
    search = explorerSearch
  ) => {
    setExplorerLoading(true);
    setExplorerError(null);
    try {
      const offset = page * PAGE_SIZE;
      const searchParam = search.trim() ? `&search=${encodeURIComponent(search.trim())}` : '';
      const refreshParam = forceRefresh ? '&refresh=1' : '';
      const res = await fetch(
        `${config.restUrl}/explorer/folders?limit=${PAGE_SIZE}&offset=${offset}&filter=${filter}${searchParam}${refreshParam}`,
        { headers: getHeaders() }
      );
      if (res.ok) {
        const data = await res.json();
        setFolders(data.folders || []);
        setFoldersTotal(data.total || 0);
      } else {
        const errText = await res.text();
        setExplorerError(`Scan request failed (HTTP ${res.status}). Click Refresh to retry.`);
        console.error('Failed to load folders:', res.status, errText);
      }
    } catch (err) {
      setExplorerError('Network error or server timeout while scanning folders.');
      console.error('Failed to load folders:', err);
    } finally {
      setExplorerLoading(false);
    }
  };

  const handleProviderChange = (e) => {
    const p = e.target.value;
    const preset = PRESETS[p];
    if (preset) {
      setSettings((prev) => ({
        ...prev,
        provider: p,
        endpoint: preset.endpoint,
        region: preset.region,
        use_path_style: preset.use_path_style,
      }));
    } else {
      setSettings((prev) => ({ ...prev, provider: p }));
    }
  };

  const handleSave = async (e) => {
    if (e) e.preventDefault();
    setSaving(true);
    try {
      const res = await fetch(`${config.restUrl}/settings`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(settings),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', 'Settings saved successfully.');
        fetchStats();
      } else {
        showNotice('error', data.message || 'Failed to save settings.');
      }
    } catch (err) {
      showNotice('error', 'Network error while saving settings: ' + err.message);
    } finally {
      setSaving(false);
    }
  };

  const handleTestConnection = async () => {
    setTesting(true);
    try {
      const res = await fetch(`${config.restUrl}/test-connection`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(settings),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', data.message || 'Connection verified successfully! Put, Get, and Delete object passed.');
      } else {
        showNotice('error', data.message || 'Connection test failed. Please verify credentials and bucket permissions.');
      }
    } catch (err) {
      showNotice('error', 'Connection test error: ' + err.message);
    } finally {
      setTesting(false);
    }
  };

  const handlePurgeResyncFonts = async () => {
    if (!window.confirm('This will purge remote fonts on S3/CDN and re-upload all local fonts with 30-day immutable caching. Server local fonts remain intact for TCPDF. Proceed?')) {
      return;
    }
    setSyncingFonts(true);
    try {
      const res = await fetch(`${config.restUrl}/fonts/purge-resync`, {
        method: 'POST',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', `${data.message} (${data.synced_count} fonts uploaded).`);
        fetchStats();
      } else {
        showNotice('error', data.message || 'Font sync failed.');
      }
    } catch (err) {
      showNotice('error', 'Font sync error: ' + err.message);
    } finally {
      setSyncingFonts(false);
    }
  };

  const handleSweepNow = async () => {
    if (!window.confirm('Run manual cold sweep now? Folders inactive past the retention period will be copied to S3 and verified before local heavy assets are purged.')) {
      return;
    }
    setSweeping(true);
    try {
      const res = await fetch(`${config.restUrl}/sweep-now`, {
        method: 'POST',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', `${data.message} Processed ${data.processed_count} folders.`);
        fetchStats();
        fetchExplorerFolders();
      } else {
        showNotice('error', data.message || 'Sweep execution failed.');
      }
    } catch (err) {
      showNotice('error', 'Sweep error: ' + err.message);
    } finally {
      setSweeping(false);
    }
  };

  const handleRestoreWhitelistedTemplates = async () => {
    if (!window.confirm('Scan all whitelisted admin templates in database and download any missing assets from S3 back to high-performance local SSD?')) {
      return;
    }
    setRestoringTemplates(true);
    try {
      const res = await fetch(`${config.restUrl}/whitelist/restore-all`, {
        method: 'POST',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', data.message);
        fetchStats();
        fetchExplorerFolders();
      } else {
        showNotice('error', data.message || 'Failed to restore whitelisted templates.');
      }
    } catch (err) {
      showNotice('error', 'Restore error: ' + err.message);
    } finally {
      setRestoringTemplates(false);
    }
  };

  // Sandbox Explorer actions
  const handleTestBatchOffload = async (count = batchCount) => {
    if (!window.confirm(`Test offload a small batch of ${count} aged folders to S3 and purge local copies? You can restore them back anytime.`)) {
      return;
    }
    setBatchRunning(true);
    try {
      const res = await fetch(`${config.restUrl}/explorer/test-batch`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({ count }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', data.message);
        fetchStats();
        fetchExplorerFolders(explorerFilter, true, explorerPage, explorerSearch);
      } else {
        showNotice('error', data.message || 'Batch test failed.');
      }
    } catch (err) {
      showNotice('error', 'Batch test error: ' + err.message);
    } finally {
      setBatchRunning(false);
    }
  };

  const handleOffloadSingleFolder = async (folder) => {
    setOperatingFolder(folder);
    try {
      const res = await fetch(`${config.restUrl}/explorer/offload-single`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({ folder }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', `Folder "${folder}" successfully offloaded to S3 and local heavy files purged.`);
        fetchStats();
        fetchExplorerFolders(explorerFilter, true, explorerPage, explorerSearch);
      } else {
        showNotice('error', data.message || 'Failed to offload folder.');
      }
    } catch (err) {
      showNotice('error', 'Offload error: ' + err.message);
    } finally {
      setOperatingFolder(null);
    }
  };

  const handleRestoreSingleFolder = async (folder) => {
    setOperatingFolder(folder);
    try {
      const res = await fetch(`${config.restUrl}/explorer/restore-single`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({ folder }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showNotice('success', `Folder "${folder}" downloaded from S3 and restored back to local NVMe SSD!`);
        fetchStats();
        fetchExplorerFolders(explorerFilter, true, explorerPage, explorerSearch);
      } else {
        showNotice('error', data.message || 'Failed to restore folder.');
      }
    } catch (err) {
      showNotice('error', 'Restore error: ' + err.message);
    } finally {
      setOperatingFolder(null);
    }
  };

  return (
    <div className="bg-slate-50 min-h-screen text-slate-800 p-2 sm:p-6 font-sans">
      {/* Top Hero Banner */}
      <div className="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white rounded-2xl p-4 sm:p-5 shadow-xl mb-4 relative overflow-hidden">
        <div className="absolute right-0 top-0 translate-x-10 -translate-y-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-3 relative z-10">
          <div>
            <div className="flex items-center gap-2 flex-wrap">
              <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2.5">
                <Cloud className="w-6 h-6 text-indigo-400" /> NBDesigner S3 Offload
              </h1>
              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-500/20 text-indigo-200 border border-indigo-400/20">
                <Sparkles className="w-3 h-3 text-indigo-300" /> Tiered Storage Engine
              </span>
              <span className="px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-400/20">
                v{window.nbdS3OffloadConfig?.version || '0.0.12'} • PHP 8.3 & HPOS
              </span>
            </div>
            <p className="text-indigo-200/80 text-xs mt-1 max-w-2xl leading-normal">
              Tiered storage lifecycle: Whitelist protected admin templates on NVMe SSD, offload aged guest designs to S3, and dual-sync fonts.
            </p>
          </div>

          <div className="flex items-center gap-2 self-start md:self-center flex-shrink-0">
            <button
              onClick={handleSave}
              disabled={saving}
              className="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-semibold shadow-md shadow-indigo-500/30 transition disabled:opacity-50 cursor-pointer text-xs sm:text-sm"
            >
              {saving ? <RefreshCw className="w-3.5 h-3.5 animate-spin" /> : <Check className="w-3.5 h-3.5" />}
              {saving ? 'Saving...' : 'Save Settings'}
            </button>
          </div>
        </div>

        {/* Live Operational Status & Master Pause/Resume Bar */}
        <div className="mt-3.5 pt-3 border-t border-indigo-700/50 grid grid-cols-1 md:grid-cols-3 gap-2.5 relative z-10">
          {/* Card 1: Bucket Connection Status */}
          <div className="bg-indigo-950/60 backdrop-blur-sm border border-indigo-500/30 rounded-xl p-2.5 sm:px-3 flex items-center justify-between">
            <div className="flex items-center gap-2.5 min-w-0">
              <div className={`w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 ${
                bucketConnection.connected ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-400/30' : 'bg-rose-500/20 text-rose-400 border border-rose-400/30'
              }`}>
                {bucketConnection.connected ? <Wifi className="w-3.5 h-3.5" /> : <WifiOff className="w-3.5 h-3.5" />}
              </div>
              <div className="min-w-0">
                <div className="text-[10px] font-bold uppercase tracking-wider text-indigo-300">S3 Bucket</div>
                <div className="text-xs font-semibold text-white truncate">
                  {bucketConnection.connected ? (
                    <span className="text-emerald-300 flex items-center gap-1">
                      <span className="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                      {bucketConnection.bucket} ({bucketConnection.latency_ms}ms)
                    </span>
                  ) : (
                    <span className="text-rose-300 flex items-center gap-1">
                      <span className="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                      {settings.bucket ? 'Connection Failed' : 'Not Configured'}
                    </span>
                  )}
                </div>
              </div>
            </div>
            <button
              onClick={() => fetchConnectionStatus(true)}
              disabled={checkingConnection}
              title="Test connection to bucket"
              className="p-1 hover:bg-indigo-800/60 text-indigo-200 hover:text-white rounded-lg transition cursor-pointer"
            >
              <RefreshCw className={`w-3 h-3 ${checkingConnection ? 'animate-spin' : ''}`} />
            </button>
          </div>

          {/* Card 2: Sync Running Indicator */}
          <div className="bg-indigo-950/60 backdrop-blur-sm border border-indigo-500/30 rounded-xl p-2.5 sm:px-3 flex items-center justify-between">
            <div className="flex items-center gap-2.5 min-w-0">
              <div className={`w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 ${
                syncStatus.is_running ? 'bg-indigo-500/30 text-indigo-300 animate-pulse border border-indigo-400/40' : 'bg-slate-700/40 text-slate-400 border border-slate-600/30'
              }`}>
                {syncStatus.is_running ? <RefreshCw className="w-3.5 h-3.5 animate-spin text-indigo-300" /> : <Activity className="w-3.5 h-3.5" />}
              </div>
              <div className="min-w-0">
                <div className="text-[10px] font-bold uppercase tracking-wider text-indigo-300">Sync Activity</div>
                <div className="text-xs font-semibold text-white truncate">
                  {syncStatus.is_running ? (
                    <span className="text-indigo-300 font-bold flex items-center gap-1.5">
                      <span className="relative flex h-2 w-2">
                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span className="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                      </span>
                      Syncing: {syncStatus.current_action}
                    </span>
                  ) : syncStatus.is_paused ? (
                    <span className="text-amber-300">⏸️ Paused (Safe Mode)</span>
                  ) : (
                    <span className="text-emerald-300">🟢 Idle (Auto-Sync On)</span>
                  )}
                </div>
              </div>
            </div>
            <button
              onClick={fetchStats}
              title="Refresh sync status"
              className="p-1 hover:bg-indigo-800/60 text-indigo-200 hover:text-white rounded-lg transition cursor-pointer"
            >
              <RefreshCw className={`w-3 h-3 ${loading ? 'animate-spin' : ''}`} />
            </button>
          </div>

          {/* Card 3: Master Pause / Resume Kill-Switch */}
          <div className={`backdrop-blur-sm border rounded-xl p-2.5 sm:px-3 flex items-center justify-between transition ${
            syncStatus.is_paused
              ? 'bg-amber-950/40 border-amber-500/40'
              : 'bg-emerald-950/40 border-emerald-500/40'
          }`}>
            <div className="flex items-center gap-2.5 min-w-0">
              <div className={`w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 ${
                syncStatus.is_paused
                  ? 'bg-amber-500/20 text-amber-300 border border-amber-400/30'
                  : 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30'
              }`}>
                {syncStatus.is_paused ? <Pause className="w-3.5 h-3.5" /> : <Play className="w-3.5 h-3.5" />}
              </div>
              <div className="min-w-0">
                <div className="text-[10px] font-bold uppercase tracking-wider text-indigo-200">
                  {syncStatus.is_paused ? 'Safe Mode Active' : 'Auto Sync State'}
                </div>
                <div className="text-xs font-bold text-white truncate">
                  {syncStatus.is_paused ? 'Uploads & Deletions Paused' : 'Scheduled Sweeps Running'}
                </div>
              </div>
            </div>

            <button
              type="button"
              onClick={handleTogglePause}
              disabled={togglingPause}
              className={`px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-sm ${
                syncStatus.is_paused
                  ? 'bg-amber-500 hover:bg-amber-400 text-slate-950'
                  : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950'
              }`}
            >
              {togglingPause ? (
                <RefreshCw className="w-3 h-3 animate-spin" />
              ) : syncStatus.is_paused ? (
                <Play className="w-3 h-3 fill-current" />
              ) : (
                <Pause className="w-3 h-3 fill-current" />
              )}
              {syncStatus.is_paused ? 'Resume' : 'Pause'}
            </button>
          </div>
        </div>

        {/* Global Toast Notice */}
        {notice && (
          <div
            className={`mt-3 p-3 rounded-xl border text-xs sm:text-sm flex items-center justify-between shadow-sm animate-fade-in ${
              notice.type === 'success'
                ? 'bg-emerald-950/80 border-emerald-500/50 text-emerald-200'
                : notice.type === 'warning'
                ? 'bg-amber-950/80 border-amber-500/50 text-amber-200'
                : 'bg-rose-950/80 border-rose-500/50 text-rose-200'
            }`}
          >
            <div className="flex items-center gap-2">
              {notice.type === 'success' ? (
                <CheckCircle2 className="w-4 h-4 text-emerald-400 flex-shrink-0" />
              ) : notice.type === 'warning' ? (
                <Pause className="w-4 h-4 text-amber-400 flex-shrink-0" />
              ) : (
                <AlertCircle className="w-4 h-4 text-rose-400 flex-shrink-0" />
              )}
              <span className="font-medium">{notice.message}</span>
            </div>
            <button
              onClick={() => setNotice(null)}
              className="text-slate-400 hover:text-white text-xs font-bold px-2 py-0.5"
            >
              ✕
            </button>
          </div>
        )}

        {/* Nav Tabs Bar */}
        <div className="flex flex-wrap gap-1.5 mt-3.5 pt-2.5 border-t border-indigo-700/40">
          <button
            onClick={() => {
              setActiveTab('explorer');
              fetchExplorerFolders();
            }}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer ${
              activeTab === 'explorer'
                ? 'bg-white text-indigo-900 shadow-sm'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Folder className="w-3.5 h-3.5" /> 🧪 Trial Sandbox & Explorer
          </button>

          <button
            onClick={() => setActiveTab('connection')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer ${
              activeTab === 'connection'
                ? 'bg-white text-indigo-900 shadow-sm'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Cloud className="w-3.5 h-3.5" /> Cloud S3 & CDN
          </button>

          <button
            onClick={() => setActiveTab('lifecycle')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer ${
              activeTab === 'lifecycle'
                ? 'bg-white text-indigo-900 shadow-sm'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Layers className="w-3.5 h-3.5" /> Tiered Storage Policy
          </button>

          <button
            onClick={() => {
              setActiveTab('logs');
              fetchLogs();
            }}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer ${
              activeTab === 'logs'
                ? 'bg-white text-indigo-900 shadow-sm'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <ScrollText className="w-3.5 h-3.5" /> Activity Log
            {logs.length > 0 && (
              <span className="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-indigo-500 text-white">
                {logs.length}
              </span>
            )}
          </button>

          <button
            onClick={() => setActiveTab('fonts')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer ${
              activeTab === 'fonts'
                ? 'bg-white text-indigo-900 shadow-sm'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Type className="w-3.5 h-3.5" /> Font Dual-Sync
          </button>

          <button
            onClick={() => {
              setActiveTab('stats');
              fetchStats();
            }}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer ${
              activeTab === 'stats'
                ? 'bg-white text-indigo-900 shadow-sm'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <BarChart3 className="w-3.5 h-3.5" /> Storage Stats & Sweeps
          </button>
        </div>
      </div>

      {/* Main Content View */}
      <div className="max-w-7xl mx-auto space-y-6">

        {/* TAB 1: TRIAL SANDBOX & FILE EXPLORER */}
        {activeTab === 'explorer' && (
          <div className="space-y-6">
            {/* Safe Batch Trial Card */}
            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                <div>
                  <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 mb-2">
                    <Zap className="w-3.5 h-3.5 text-amber-600" /> Safe Trial Zone
                  </div>
                  <h2 className="text-lg font-bold text-slate-900">
                    Try Offloading a Small Batch of Aged Folders
                  </h2>
                  <p className="text-sm text-slate-500 mt-1">
                    Instead of offloading all aged files at once, test S3 upload and local purge on a controlled trial batch. You can restore them back locally anytime with one click!
                  </p>
                </div>

                <div className="flex items-center gap-3">
                  <div className="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-semibold text-slate-700">
                    <span>Batch:</span>
                    {[1, 3, 5, 10].map((n) => (
                      <button
                        key={n}
                        onClick={() => setBatchCount(n)}
                        className={`px-2.5 py-1 rounded-lg transition ${
                          batchCount === n ? 'bg-white text-indigo-700 shadow-sm' : 'hover:text-indigo-600'
                        }`}
                      >
                        {n}
                      </button>
                    ))}
                  </div>

                  <button
                    onClick={() => handleTestBatchOffload(batchCount)}
                    disabled={batchRunning}
                    className="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-sm transition text-xs sm:text-sm cursor-pointer disabled:opacity-50 whitespace-nowrap"
                  >
                    {batchRunning ? (
                      <RefreshCw className="w-4 h-4 animate-spin" />
                    ) : (
                      <UploadCloud className="w-4 h-4" />
                    )}
                    {batchRunning ? 'Processing Trial...' : `Test Offload ${batchCount} Aged Folders`}
                  </button>
                </div>
              </div>

              {/* Filter and Folder Explorer Controls */}
              <div className="mt-6">
                <div className="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
                  {/* Filters */}
                  <div className="flex flex-wrap items-center gap-2">
                    <span className="text-xs font-bold uppercase tracking-wider text-slate-400">
                      Filter:
                    </span>
                    {[
                      { key: 'all', label: 'All Folders' },
                      { key: 'aged', label: 'Aged (> 14d)' },
                      { key: 'offloaded', label: 'Offloaded to S3' },
                      { key: 'templates', label: 'Whitelisted Templates' },
                    ].map((f) => (
                      <button
                        key={f.key}
                        onClick={() => {
                          setExplorerFilter(f.key);
                          setExplorerPage(0);
                          fetchExplorerFolders(f.key, false, 0, explorerSearch);
                        }}
                        className={`px-3 py-1 rounded-lg text-xs font-semibold transition cursor-pointer ${
                          explorerFilter === f.key
                            ? 'bg-slate-900 text-white'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        }`}
                      >
                        {f.label}
                      </button>
                    ))}
                  </div>

                  {/* Search and Pagination Controls */}
                  <div className="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-between lg:justify-end">
                    {/* Search box */}
                    <div className="relative">
                      <Search className="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" />
                      <input
                        type="text"
                        placeholder="Search folder ID..."
                        value={explorerSearch}
                        onChange={(e) => setExplorerSearch(e.target.value)}
                        onKeyDown={(e) => {
                          if (e.key === 'Enter') {
                            setExplorerPage(0);
                            fetchExplorerFolders(explorerFilter, false, 0, e.target.value);
                          }
                        }}
                        className="pl-8 pr-3 py-1 bg-slate-100 hover:bg-slate-200/70 focus:bg-white focus:ring-2 focus:ring-indigo-500 rounded-lg text-xs text-slate-800 placeholder-slate-400 transition outline-none w-40 sm:w-48"
                      />
                    </div>

                    {/* Pagination & Count */}
                    <div className="flex items-center gap-2 text-xs text-slate-500">
                      <span>
                        {foldersTotal > 0
                          ? `Showing ${explorerPage * PAGE_SIZE + 1}–${Math.min((explorerPage + 1) * PAGE_SIZE, foldersTotal)} of ${foldersTotal}`
                          : 'Showing 0 folders'}
                      </span>

                      <div className="flex items-center gap-1 border-l border-slate-200 pl-2">
                        <button
                          onClick={() => {
                            const newPage = Math.max(0, explorerPage - 1);
                            setExplorerPage(newPage);
                            fetchExplorerFolders(explorerFilter, false, newPage, explorerSearch);
                          }}
                          disabled={explorerPage === 0 || explorerLoading}
                          className="p-1 hover:bg-slate-100 disabled:opacity-30 rounded text-slate-600 transition cursor-pointer disabled:cursor-not-allowed"
                          title="Previous Page"
                        >
                          <ChevronLeft className="w-4 h-4" />
                        </button>
                        <span className="text-slate-500 font-medium px-1">
                          {Math.max(1, explorerPage + 1)} / {Math.max(1, Math.ceil(foldersTotal / PAGE_SIZE))}
                        </span>
                        <button
                          onClick={() => {
                            const maxPage = Math.ceil(foldersTotal / PAGE_SIZE) - 1;
                            const newPage = Math.min(maxPage, explorerPage + 1);
                            setExplorerPage(newPage);
                            fetchExplorerFolders(explorerFilter, false, newPage, explorerSearch);
                          }}
                          disabled={explorerPage >= Math.ceil(foldersTotal / PAGE_SIZE) - 1 || explorerLoading}
                          className="p-1 hover:bg-slate-100 disabled:opacity-30 rounded text-slate-600 transition cursor-pointer disabled:cursor-not-allowed"
                          title="Next Page"
                        >
                          <ChevronRight className="w-4 h-4" />
                        </button>
                      </div>

                      {/* Force Refresh */}
                      <button
                        onClick={() => fetchExplorerFolders(explorerFilter, true, explorerPage, explorerSearch)}
                        disabled={explorerLoading}
                        className="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 transition cursor-pointer ml-1"
                        title="Force Rescan"
                      >
                        <RefreshCw className={`w-3.5 h-3.5 ${explorerLoading ? 'animate-spin text-indigo-600' : ''}`} />
                      </button>
                    </div>
                  </div>
                </div>

                {/* Table */}
                <div className="border border-slate-200 rounded-xl overflow-hidden shadow-xs bg-white">
                  <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse text-xs sm:text-sm">
                      <thead>
                        <tr className="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                          <th className="py-3 px-4">Design Folder Key</th>
                          <th className="py-3 px-4">Storage Status</th>
                          <th className="py-3 px-4">Age / Modified</th>
                          <th className="py-3 px-4">Local Files / Size</th>
                          <th className="py-3 px-4 text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-slate-100">
                        {folders.length === 0 ? (
                          <tr>
                            <td colSpan={5} className="py-8 text-center text-slate-400">
                              {explorerLoading ? (
                                <div className="flex items-center justify-center gap-2">
                                  <RefreshCw className="w-4 h-4 animate-spin text-indigo-500" />
                                  <span>Scanning design directory...</span>
                                </div>
                              ) : explorerError ? (
                                <div className="text-rose-500 font-medium">{explorerError}</div>
                              ) : (
                                'No design folders found matching this filter.'
                              )}
                            </td>
                          </tr>
                        ) : (
                          folders.map((f) => {
                            const isBusy = operatingFolder === f.folder;
                            return (
                              <tr key={f.folder} className="hover:bg-slate-50/80 transition">
                                <td className="py-3 px-4 font-mono font-medium text-slate-800">
                                  <div className="flex items-center gap-2">
                                    <Folder className="w-4 h-4 text-slate-400 flex-shrink-0" />
                                    <span className="truncate max-w-[200px] sm:max-w-xs">{f.folder}</span>
                                  </div>
                                </td>

                                <td className="py-3 px-4">
                                  {f.is_template ? (
                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                      <ShieldCheck className="w-3 h-3 text-blue-600" /> Tier 1 Whitelist
                                    </span>
                                  ) : f.is_offloaded ? (
                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                      <Cloud className="w-3 h-3 text-purple-600" /> Offloaded in S3
                                    </span>
                                  ) : f.is_aged ? (
                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                      <HardDrive className="w-3 h-3 text-amber-600" /> Aged (Cold Candidate)
                                    </span>
                                  ) : (
                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                      <HardDrive className="w-3 h-3 text-emerald-600" /> Active Local SSD
                                    </span>
                                  )}
                                </td>

                                <td className="py-3 px-4 text-slate-600 whitespace-nowrap">
                                  <div>{f.modified_date}</div>
                                  <span className="text-xs text-slate-400">{f.age_days} days ago</span>
                                </td>

                                <td className="py-3 px-4 text-slate-600 whitespace-nowrap">
                                  <span className="font-semibold text-slate-700">{f.size_formatted}</span>
                                  <span className="text-xs text-slate-400 ml-1.5">({f.files_count} files)</span>
                                </td>

                                <td className="py-3 px-4 text-right whitespace-nowrap">
                                  <div className="flex items-center justify-end gap-2">
                                    {/* Action: Offload */}
                                    {!f.is_template && !f.is_offloaded && (
                                      <button
                                        onClick={() => handleOffloadSingleFolder(f.folder)}
                                        disabled={isBusy}
                                        className="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-semibold transition cursor-pointer"
                                      >
                                        {isBusy ? (
                                          <RefreshCw className="w-3 h-3 animate-spin" />
                                        ) : (
                                          <UploadCloud className="w-3 h-3" />
                                        )}
                                        Offload to S3
                                      </button>
                                    )}

                                    {/* Action: Restore */}
                                    {f.is_offloaded && (
                                      <button
                                        onClick={() => handleRestoreSingleFolder(f.folder)}
                                        disabled={isBusy}
                                        className="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-semibold transition cursor-pointer"
                                      >
                                        {isBusy ? (
                                          <RefreshCw className="w-3 h-3 animate-spin" />
                                        ) : (
                                          <Download className="w-3 h-3" />
                                        )}
                                        Restore Back to Local
                                      </button>
                                    )}

                                    {/* Protected Tier 1 Whitelist - Always Local */}
                                    {f.is_template && (
                                      <span className="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 text-slate-500 border border-slate-200 rounded-lg text-xs font-medium">
                                        <ShieldCheck className="w-3.5 h-3.5 text-blue-600" /> Protected Local
                                      </span>
                                    )}
                                  </div>
                                </td>
                              </tr>
                            );
                          })
                        )}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* TAB 2: CLOUD S3 CONNECTION */}
        {activeTab === 'connection' && (
          <div className="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div className="border-b border-slate-100 pb-4">
              <h2 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                <Cloud className="w-5 h-5 text-indigo-600" /> S3-Compatible Cloud Storage Credentials
              </h2>
              <p className="text-sm text-slate-500 mt-1">
                Configure your cold storage bucket. Requests are securely signed with AWS Signature Version 4 in pure PHP 8.3 with zero external dependencies.
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Storage Provider Preset
                </label>
                <select
                  value={settings.provider}
                  onChange={handleProviderChange}
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition cursor-pointer"
                >
                  <option value="wasabi">Wasabi Cloud Storage</option>
                  <option value="backblaze">Backblaze B2 (S3 Compatible)</option>
                  <option value="bunny">Bunny Storage / BunnyCDN</option>
                  <option value="digitalocean">DigitalOcean Spaces</option>
                  <option value="aws">Amazon Web Services S3</option>
                  <option value="custom">Custom S3 Endpoint (MinIO, Ceph, etc.)</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Region
                </label>
                <input
                  type="text"
                  value={settings.region}
                  onChange={(e) => setSettings({ ...settings, region: e.target.value })}
                  placeholder="e.g. us-east-1, us-east-005, nyc3"
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Endpoint Host
                </label>
                <input
                  type="text"
                  value={settings.endpoint}
                  onChange={(e) => setSettings({ ...settings, endpoint: e.target.value })}
                  placeholder="e.g. s3.wasabisys.com"
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
                <p className="text-xs text-slate-400 mt-1">Host without protocol (e.g. <code>s3.wasabisys.com</code>)</p>
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Bucket Name
                </label>
                <input
                  type="text"
                  value={settings.bucket}
                  onChange={(e) => setSettings({ ...settings, bucket: e.target.value })}
                  placeholder="your-company-nbdesigner-assets"
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Access Key ID
                </label>
                <input
                  type="text"
                  value={settings.access_key}
                  onChange={(e) => setSettings({ ...settings, access_key: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                  autoComplete="off"
                />
              </div>

              <div>
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Secret Access Key
                </label>
                <input
                  type="password"
                  value={settings.secret_key}
                  onChange={(e) => setSettings({ ...settings, secret_key: e.target.value })}
                  placeholder={settings.secret_key ? '••••••••••••••••' : 'Enter Secret Key'}
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                  autoComplete="new-password"
                />
              </div>

              <div className="md:col-span-2">
                <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                  Public CDN / Custom Domain Base URL (Optional)
                </label>
                <input
                  type="url"
                  value={settings.cdn_url}
                  onChange={(e) => setSettings({ ...settings, cdn_url: e.target.value })}
                  placeholder="https://cdn.yourdomain.com or https://your-pullzone.b-cdn.net"
                  className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
                <p className="text-xs text-slate-400 mt-1">
                  Optional CDN edge URL for high-speed font caching and browser delivery.
                </p>
              </div>

              <div className="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200 flex items-start justify-between gap-4">
                <div className="flex items-start gap-3">
                  <div className="mt-0.5">
                    <ToggleSwitch
                      checked={settings.use_path_style}
                      onChange={(val) => setSettings({ ...settings, use_path_style: val })}
                      activeColor="bg-indigo-600"
                      ariaLabel="Use Path-Style URLs"
                    />
                  </div>
                  <div>
                    <span className="text-sm font-bold text-slate-800">
                      Use Path-Style URLs (<code>endpoint/bucket/...</code>)
                    </span>
                    <p className="text-xs text-slate-500 mt-0.5">
                      Recommended for Wasabi, Backblaze B2, MinIO, and DigitalOcean Spaces. (Turn off for standard AWS S3 virtual-hosted buckets).
                    </p>
                  </div>
                </div>
                <span className={`text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0 ${
                  settings.use_path_style ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-600'
                }`}>
                  {settings.use_path_style ? 'Path-Style ON' : 'Virtual-Host'}
                </span>
              </div>
            </div>

            <div className="pt-6 border-t border-slate-100 flex items-center justify-between">
              <button
                type="button"
                onClick={handleTestConnection}
                disabled={testing}
                className="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-semibold transition cursor-pointer text-sm"
              >
                {testing ? <RefreshCw className="w-4 h-4 animate-spin text-slate-600" /> : <Zap className="w-4 h-4 text-indigo-600" />}
                {testing ? 'Testing Handshake...' : 'Test S3 Connection'}
              </button>

              <button
                type="button"
                onClick={handleSave}
                disabled={saving}
                className="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-md shadow-indigo-500/20 transition cursor-pointer text-sm"
              >
                {saving ? 'Saving...' : 'Save Settings'}
              </button>
            </div>
          </div>
        )}

        {/* TAB 3: LIFECYCLE & STORAGE POLICY */}
        {activeTab === 'lifecycle' && (
          <div className="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div className="border-b border-slate-100 pb-4">
              <h2 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                <Layers className="w-5 h-5 text-indigo-600" /> Tiered Storage Lifecycle Policy
              </h2>
              <p className="text-sm text-slate-500 mt-1">
                Protect shop performance by retaining templates on fast NVMe SSD while offloading old one-time orders to low-cost S3.
              </p>
            </div>

            {/* Tier 1 Whitelist Card */}
            <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5">
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-start gap-3.5">
                  <div className="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <ShieldCheck className="w-5 h-5" />
                  </div>
                  <div>
                    <h3 className="text-sm font-bold text-blue-950">
                      Tier 1: Admin Template Protection Whitelist (Local NVMe SSD)
                    </h3>
                    <p className="text-xs text-blue-800 mt-1 leading-relaxed">
                      All design folders registered in <code>wp_nbdesigner_templates</code> are protected. They are never moved or deleted by background sweeps.
                    </p>
                    <p className="text-xs text-blue-700 mt-1">
                      If you convert older offloaded guest orders into admin templates, click Restore below to pull their assets back from S3.
                    </p>
                  </div>
                </div>

                <button
                  type="button"
                  onClick={handleRestoreWhitelistedTemplates}
                  disabled={restoringTemplates}
                  className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition whitespace-nowrap self-start md:self-center cursor-pointer"
                >
                  {restoringTemplates ? <RefreshCw className="w-3.5 h-3.5 animate-spin" /> : <Download className="w-3.5 h-3.5" />}
                  {restoringTemplates ? 'Restoring...' : 'Restore Whitelisted Templates to Local'}
                </button>
              </div>
            </div>

            {/* Master Safety Pause & Schedule Controls */}
            <div className="bg-slate-50 rounded-2xl border border-slate-200 p-5 space-y-5">
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div className="flex items-start gap-3">
                  <div className={`w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 ${
                    settings.sync_paused ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'
                  }`}>
                    {settings.sync_paused ? <Pause className="w-5 h-5" /> : <Play className="w-5 h-5" />}
                  </div>
                  <div>
                    <h3 className="text-sm font-bold text-slate-900">
                      Master Sync & Purge Switch
                    </h3>
                    <p className="text-xs text-slate-500 mt-0.5">
                      {settings.sync_paused
                        ? 'Safe Mode Active: All automatic S3 uploads and local file purges are completely stopped.'
                        : 'Active: Automated daily cold sweeps and draft cleanup are scheduled and running.'}
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-3 self-start md:self-center bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                  <span className={`text-xs font-bold ${settings.sync_paused ? 'text-amber-800' : 'text-emerald-800'}`}>
                    {settings.sync_paused ? '⏸️ Paused (Safe)' : '▶️ Syncing Active'}
                  </span>
                  <ToggleSwitch
                    checked={!settings.sync_paused}
                    onChange={(val) => setSettings({ ...settings, sync_paused: !val })}
                    activeColor="bg-emerald-600"
                    ariaLabel="Toggle Master Sync & Purge Switch"
                  />
                </div>
              </div>

              {/* Scheduled Run Time & Server Time Display */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="bg-white p-4 rounded-xl border border-slate-200">
                  <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <Clock className="w-4 h-4 text-indigo-600" /> Daily Sync Scheduled Run Time
                  </label>
                  <input
                    type="time"
                    value={settings.sync_schedule_time || '03:00'}
                    onChange={(e) => setSettings({ ...settings, sync_schedule_time: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-sm font-mono font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500 transition"
                  />
                  <p className="text-xs text-slate-400 mt-1">
                    Triggers once daily at this local server hour via Action Scheduler.
                  </p>
                </div>

                <div className="bg-gradient-to-br from-indigo-50/70 to-slate-50 p-4 rounded-xl border border-indigo-100 flex flex-col justify-between">
                  <div>
                    <span className="text-xs font-bold uppercase tracking-wider text-indigo-700 flex items-center gap-1.5">
                      <Radio className="w-3.5 h-3.5 text-indigo-500 animate-pulse" /> Live Server Clock
                    </span>
                    <div className="text-lg font-mono font-extrabold text-slate-900 mt-1">
                      {syncStatus.server_time || 'Loading time...'}
                    </div>
                  </div>
                  <div className="flex items-center justify-between text-xs text-indigo-900/80 pt-2 border-t border-indigo-100 mt-2">
                    <span>Timezone: <strong>{syncStatus.server_timezone || 'UTC'}</strong></span>
                    {syncStatus.next_scheduled_run && (
                      <span className="text-[11px] text-slate-500">Next run: {syncStatus.next_scheduled_run}</span>
                    )}
                  </div>
                </div>
              </div>
            </div>

            {/* Tier 2 Hot Active Orders Card */}
            <div className="bg-gradient-to-r from-amber-50/80 to-orange-50/50 border border-amber-200 rounded-2xl p-5">
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-start gap-3.5">
                  <div className="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <Flame className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="text-sm font-bold text-amber-950">
                        Tier 2: Hot Active Orders (Local NVMe SSD Fast Fulfillment)
                      </h3>
                      <span className="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        0 to {settings.cold_retention_days} Days
                      </span>
                    </div>
                    <p className="text-xs text-amber-900/90 mt-1 leading-relaxed">
                      All customer designs for active production and orders younger than <strong>{settings.cold_retention_days} days</strong> are kept 100% on fast local NVMe SSD.
                    </p>
                    <p className="text-xs text-amber-800/80 mt-1">
                      <strong>Why:</strong> Factory print production, RIP queues, and TCPDF rendering engines require zero-latency local disk access during active manufacturing. Once an order passes {settings.cold_retention_days} days, it automatically transitions into <strong>Tier 3 Cold Storage</strong>.
                    </p>
                  </div>
                </div>

                <div className="bg-white/90 border border-amber-200 px-3.5 py-2 rounded-xl text-center self-start md:self-center flex-shrink-0 shadow-xs">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Fulfillment SLA</span>
                  <span className="text-xs font-extrabold text-amber-900 flex items-center gap-1.5 justify-center mt-0.5">
                    <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> 100% Zero-Latency SSD
                  </span>
                </div>
              </div>
            </div>

            {/* Tier 3 Cold Order Offload Setting */}
            <div className="border border-indigo-200 bg-gradient-to-r from-indigo-50/50 to-slate-50/50 rounded-2xl p-5 space-y-4">
              <div className="flex items-start justify-between gap-4">
                <div className="flex items-start gap-3.5">
                  <div className="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <Archive className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="text-sm font-bold text-slate-900">
                        Tier 3: Cold Storage Offload (S3 Archiving & Smart Purge)
                      </h3>
                      <span className="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                        &gt; {settings.cold_retention_days} Days
                      </span>
                    </div>
                    <p className="text-xs text-slate-600 mt-1 leading-relaxed">
                      Completed order designs older than {settings.cold_retention_days} days are copied to S3, verified via MD5/ETag checksums, and local disk copies purged to reclaim SSD storage.
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-2.5 self-start md:self-center bg-white px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-sm flex-shrink-0">
                  <span className={`text-xs font-bold ${settings.enable_cold_order_offload ? 'text-indigo-900' : 'text-slate-400'}`}>
                    {settings.enable_cold_order_offload ? 'Active' : 'Disabled'}
                  </span>
                  <ToggleSwitch
                    checked={settings.enable_cold_order_offload}
                    onChange={(val) => setSettings({ ...settings, enable_cold_order_offload: val })}
                    activeColor="bg-indigo-600"
                    ariaLabel="Toggle Tier 3 Cold Storage Offload"
                  />
                </div>
              </div>

              {settings.enable_cold_order_offload && (
                <div className="border-t border-indigo-100 pt-4 space-y-5 bg-white/70 p-4 rounded-xl border border-slate-200">
                  <div>
                    <div className="flex items-center justify-between mb-2">
                      <label className="text-xs font-bold uppercase tracking-wider text-slate-700">
                        Cold Retention Threshold (Tier 2 Hot → Tier 3 Cold Transition)
                      </label>
                      <span className="text-xs font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                        {settings.cold_retention_days} Days
                      </span>
                    </div>
                    <input
                      type="range"
                      min="1"
                      max="90"
                      value={settings.cold_retention_days}
                      onChange={(e) => setSettings({ ...settings, cold_retention_days: parseInt(e.target.value, 10) })}
                      className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                    />
                    <div className="flex justify-between text-xs text-slate-400 mt-1">
                      <span>1 day (Aggressive)</span>
                      <span>14 days (Recommended)</span>
                      <span>90 days</span>
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                      Local Disk Purge Strategy:
                    </label>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <label className={`p-4 rounded-xl border cursor-pointer transition ${settings.purge_mode === 'smart' ? 'bg-indigo-50/60 border-indigo-300 ring-1 ring-indigo-400' : 'bg-white border-slate-200'}`}>
                        <div className="flex items-center gap-2 mb-1">
                          <input
                            type="radio"
                            name="purge_mode"
                            value="smart"
                            checked={settings.purge_mode === 'smart'}
                            onChange={() => setSettings({ ...settings, purge_mode: 'smart' })}
                            className="text-indigo-600"
                          />
                          <span className="text-sm font-bold text-slate-900">Smart Purge (Recommended)</span>
                        </div>
                        <p className="text-xs text-slate-500 ml-5">
                          Deletes heavy print PDFs and raw image files; retains lightweight <code>product.json</code> and preview thumbnails locally for instant admin order list rendering.
                        </p>
                      </label>

                      <label className={`p-4 rounded-xl border cursor-pointer transition ${settings.purge_mode === 'full' ? 'bg-indigo-50/60 border-indigo-300 ring-1 ring-indigo-400' : 'bg-white border-slate-200'}`}>
                        <div className="flex items-center gap-2 mb-1">
                          <input
                            type="radio"
                            name="purge_mode"
                            value="full"
                            checked={settings.purge_mode === 'full'}
                            onChange={() => setSettings({ ...settings, purge_mode: 'full' })}
                            className="text-indigo-600"
                          />
                          <span className="text-sm font-bold text-slate-900">Full Purge</span>
                        </div>
                        <p className="text-xs text-slate-500 ml-5">
                          Removes the entire design folder from local disk once confirmed in S3. If re-downloaded or viewed, automatically hydrated back on demand.
                        </p>
                      </label>
                    </div>
                  </div>
                </div>
              )}
            </div>

            {/* Tier 4 Font Dual-Sync Card */}
            <div className="bg-gradient-to-r from-purple-50/80 to-indigo-50/60 border border-purple-200 rounded-2xl p-5">
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-start gap-3.5">
                  <div className="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <Type className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="text-sm font-bold text-purple-950">
                        Tier 4: Font Dual-Sync Engine (Local NVMe SSD + CDN Edge)
                      </h3>
                      <span className="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                        Dual-Storage
                      </span>
                    </div>
                    <p className="text-xs text-purple-900/90 mt-1 leading-relaxed">
                      Typography requires a hybrid dual-sync architecture: Fonts remain on local NVMe SSD for TCPDF high-resolution vector PDF rendering, while simultaneously syncing to S3 / CloudFront with 30-day immutable caching (<code>max-age=2592000</code>) for instant browser canvas rendering.
                    </p>
                    <p className="text-xs text-purple-800/80 mt-1">
                      Font dual-sync controls, auto-sync triggers, and manual font sync buttons are managed in the dedicated <strong>Font Dual-Sync</strong> tab.
                    </p>
                  </div>
                </div>

                <button
                  type="button"
                  onClick={() => setActiveTab('fonts')}
                  className="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-sm transition whitespace-nowrap self-start md:self-center cursor-pointer"
                >
                  <span>Open Font Dual-Sync Settings</span>
                  <ExternalLink className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>

            {/* Tier 5 Draft Garbage Collection */}
            <div className="border border-slate-200 bg-slate-50/80 rounded-2xl p-5 space-y-4">
              <div className="flex items-start justify-between gap-4">
                <div className="flex items-start gap-3.5">
                  <div className="w-10 h-10 rounded-xl bg-slate-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <Trash2 className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="text-sm font-bold text-slate-900">
                        Tier 5: Garbage Collect Abandoned Guest Drafts & Temporary Uploads
                      </h3>
                      <span className="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                        &gt; {settings.abandoned_drafts_days} Days
                      </span>
                    </div>
                    <p className="text-xs text-slate-500 mt-1 leading-relaxed">
                      Cleans up orphan unpurchased session uploads and temporary files older than {settings.abandoned_drafts_days} days to keep SSD disks lean.
                    </p>
                  </div>
                </div>

                <div className="flex items-center gap-2.5 self-start md:self-center bg-white px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-sm flex-shrink-0">
                  <span className={`text-xs font-bold ${settings.cleanup_abandoned_drafts ? 'text-slate-900' : 'text-slate-400'}`}>
                    {settings.cleanup_abandoned_drafts ? 'Active' : 'Disabled'}
                  </span>
                  <ToggleSwitch
                    checked={settings.cleanup_abandoned_drafts}
                    onChange={(val) => setSettings({ ...settings, cleanup_abandoned_drafts: val })}
                    activeColor="bg-slate-700"
                    ariaLabel="Toggle Tier 5 Draft Garbage Collection"
                  />
                </div>
              </div>

              {settings.cleanup_abandoned_drafts && (
                <div className="border-t border-slate-200 pt-3">
                  <div className="flex items-center justify-between mb-2">
                    <label className="text-xs font-bold uppercase tracking-wider text-slate-700">
                      Draft Retention Window
                    </label>
                    <span className="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-800">
                      {settings.abandoned_drafts_days} Days
                    </span>
                  </div>
                  <input
                    type="range"
                    min="1"
                    max="30"
                    value={settings.abandoned_drafts_days}
                    onChange={(e) => setSettings({ ...settings, abandoned_drafts_days: parseInt(e.target.value, 10) })}
                    className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-600"
                  />
                  <div className="flex justify-between text-xs text-slate-400 mt-1">
                    <span>1 day</span>
                    <span>7 days (Recommended)</span>
                    <span>30 days</span>
                  </div>
                </div>
              )}
            </div>

              {/* Activity Log Protection Configuration */}
              <div className="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <div className="flex items-center justify-between gap-4">
                  <div className="flex items-start gap-3">
                    <div className="mt-0.5">
                      <ToggleSwitch
                        checked={settings.enable_activity_log}
                        onChange={(val) => setSettings({ ...settings, enable_activity_log: val })}
                        activeColor="bg-indigo-600"
                        ariaLabel="Toggle Activity Audit Log"
                      />
                    </div>
                    <div>
                      <span className="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <ScrollText className="w-4 h-4 text-indigo-600" /> Enable Activity Audit Log
                      </span>
                      <p className="text-xs text-slate-500 mt-0.5">
                        Records background sweep runs, manual offloads, restorations, and pause/resume events.
                      </p>
                    </div>
                  </div>

                  <span className="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 flex-shrink-0">
                    Auto Disk-Bloat Protection
                  </span>
                </div>

                {settings.enable_activity_log && (
                  <div className="ml-7 pt-3 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Max Log Entries (FIFO Auto-Pruned)
                      </label>
                      <input
                        type="number"
                        min="50"
                        max="500"
                        value={settings.max_log_entries || 200}
                        onChange={(e) => setSettings({ ...settings, max_log_entries: parseInt(e.target.value, 10) || 200 })}
                        className="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 transition"
                      />
                      <p className="text-xs text-slate-400 mt-1">
                        Automatically discards oldest logs once limit is reached. Capped to under 35KB in database.
                      </p>
                    </div>
                    <div className="bg-white p-3.5 rounded-xl border border-slate-200 flex items-center">
                      <p className="text-xs text-slate-600 leading-relaxed">
                        🛡️ <strong>Drive Safe Guarantee:</strong> Log entries are stored in a non-autoloaded WordPress option and pruned immediately on write. Unlimited disk bloat is mathematically impossible.
                      </p>
                    </div>
                  </div>
                )}
              </div>

            <div className="pt-6 border-t border-slate-100 flex justify-end">
              <button
                type="button"
                onClick={handleSave}
                disabled={saving}
                className="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-md shadow-indigo-500/20 transition cursor-pointer text-sm"
              >
                {saving ? 'Saving...' : 'Save Settings'}
              </button>
            </div>
          </div>
        )}

        {/* TAB 4: ACTIVITY AUDIT LOG */}
        {activeTab === 'logs' && (
          <div className="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
              <div>
                <h2 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                  <ScrollText className="w-5 h-5 text-indigo-600" /> Activity Audit Log
                </h2>
                <p className="text-sm text-slate-500 mt-1">
                  Trace background sweeps, trial batch tests, restorations, and master switch toggles. Entries are automatically FIFO pruned to prevent disk consumption.
                </p>
              </div>

              <div className="flex items-center gap-2.5 self-start md:self-center">
                <button
                  type="button"
                  onClick={fetchLogs}
                  disabled={logsLoading}
                  className="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer"
                >
                  <RefreshCw className={`w-3.5 h-3.5 ${logsLoading ? 'animate-spin' : ''}`} /> Refresh
                </button>
                <button
                  type="button"
                  onClick={handleClearLogs}
                  disabled={clearingLogs || logs.length === 0}
                  className="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-xl transition disabled:opacity-50 cursor-pointer"
                >
                  <Trash2 className="w-3.5 h-3.5" /> Clear Log
                </button>
              </div>
            </div>

            {/* Filter Chips */}
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-xs font-bold uppercase tracking-wider text-slate-400 mr-1 flex items-center gap-1">
                <Filter className="w-3.5 h-3.5" /> Filter:
              </span>
              {[
                { id: 'all', label: 'All Events' },
                { id: 'SWEEP', label: 'Sweeps' },
                { id: 'OFFLOAD', label: 'Offloads & Trials' },
                { id: 'RESTORE', label: 'Restorations' },
                { id: 'SYNC_', label: 'Pause / Resume' },
                { id: 'error', label: 'Errors Only' },
              ].map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setLogsFilter(tab.id)}
                  className={`px-3 py-1 rounded-lg text-xs font-semibold transition cursor-pointer ${
                    logsFilter === tab.id
                      ? 'bg-indigo-600 text-white shadow-sm'
                      : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                  }`}
                >
                  {tab.label}
                </button>
              ))}
            </div>

            {/* Log Table */}
            <div className="overflow-hidden rounded-xl border border-slate-200">
              <table className="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead className="bg-slate-50 text-slate-500 uppercase tracking-wider font-bold">
                  <tr>
                    <th className="py-3 px-4">Timestamp</th>
                    <th className="py-3 px-4">Action</th>
                    <th className="py-3 px-4">Status</th>
                    <th className="py-3 px-4">Message & Details</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100 bg-white">
                  {logsLoading ? (
                    <tr>
                      <td colSpan={4} className="py-12 text-center text-slate-400">
                        <RefreshCw className="w-5 h-5 animate-spin mx-auto mb-2 text-indigo-500" />
                        Loading activity logs...
                      </td>
                    </tr>
                  ) : logs.length === 0 ? (
                    <tr>
                      <td colSpan={4} className="py-12 text-center text-slate-400">
                        <ScrollText className="w-8 h-8 mx-auto mb-2 text-slate-300" />
                        No activity records found. Events from automated sweeps and trial runs will show up here.
                      </td>
                    </tr>
                  ) : (
                    logs
                      .filter((log) => {
                        if (logsFilter === 'all') return true;
                        if (logsFilter === 'error') return log.status === 'error';
                        return (log.action || '').toUpperCase().includes(logsFilter.toUpperCase());
                      })
                      .map((log) => (
                        <tr key={log.id} className="hover:bg-slate-50/70 transition font-mono">
                          <td className="py-3 px-4 whitespace-nowrap text-slate-500 font-sans text-xs">
                            {log.time}
                          </td>
                          <td className="py-3 px-4 whitespace-nowrap">
                            <span className={`inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold ${
                              log.action.includes('PAUSE')
                                ? 'bg-amber-100 text-amber-800 border border-amber-200'
                                : log.action.includes('RESUME')
                                ? 'bg-emerald-100 text-emerald-800 border border-emerald-200'
                                : log.action.includes('RESTORE')
                                ? 'bg-blue-100 text-blue-800 border border-blue-200'
                                : log.action.includes('SWEEP')
                                ? 'bg-purple-100 text-purple-800 border border-purple-200'
                                : 'bg-slate-100 text-slate-800 border border-slate-200'
                            }`}>
                              {log.action}
                            </span>
                          </td>
                          <td className="py-3 px-4 whitespace-nowrap font-sans">
                            <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold ${
                              log.status === 'success'
                                ? 'bg-emerald-50 text-emerald-700'
                                : log.status === 'warning'
                                ? 'bg-amber-50 text-amber-700'
                                : log.status === 'error'
                                ? 'bg-rose-50 text-rose-700'
                                : 'bg-slate-100 text-slate-600'
                            }`}>
                              <span className={`w-1.5 h-1.5 rounded-full ${
                                log.status === 'success'
                                  ? 'bg-emerald-500'
                                  : log.status === 'warning'
                                  ? 'bg-amber-500'
                                  : log.status === 'error'
                                  ? 'bg-rose-500'
                                  : 'bg-slate-400'
                              }`} />
                              {log.status.toUpperCase()}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-slate-800 font-sans text-xs leading-relaxed">
                            <div>{log.message}</div>
                            {log.context && Object.keys(log.context).length > 0 && (
                              <details className="mt-1 cursor-pointer">
                                <summary className="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold font-mono">
                                  View context metadata
                                </summary>
                                <pre className="mt-1 p-2 bg-slate-900 text-slate-100 text-[10px] rounded-lg overflow-x-auto font-mono">
                                  {JSON.stringify(log.context, null, 2)}
                                </pre>
                              </details>
                            )}
                          </td>
                        </tr>
                      ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* TAB 4: FONT DUAL-SYNC */}
        {activeTab === 'fonts' && (
          <div className="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div className="border-b border-slate-100 pb-4">
              <h2 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                <Type className="w-5 h-5 text-indigo-600" /> Tier 4: Font Dual-Sync Engine
              </h2>
              <p className="text-sm text-slate-500 mt-1">
                Maintains local fonts for TCPDF / Imagick PDF print rendering while caching fonts on S3 & CDN with 30-day immutable headers for client browsers.
              </p>
            </div>

            <div className="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3.5">
              <div className="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <Type className="w-5 h-5" />
              </div>
              <div>
                <h3 className="text-sm font-bold text-amber-950">Why Font Dual-Sync?</h3>
                <p className="text-xs text-amber-800 mt-1 leading-relaxed">
                  TCPDF requires local filesystem font definitions (<code>.ttf</code>, <code>.otf</code>, <code>.php</code>) on the web server to export high-res print files. Simultaneously, customer browsers loading canvas fonts benefit from S3/CDN edge caching with <code>Cache-Control: public, max-age=2592000, immutable</code> and CORS headers.
                </p>
              </div>
            </div>

            <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-4">
              <div className="flex items-center gap-3">
                <ToggleSwitch
                  checked={settings.enable_font_offload}
                  onChange={(val) => setSettings({ ...settings, enable_font_offload: val })}
                  activeColor="bg-indigo-600"
                  ariaLabel="Toggle Serving Studio Frontend Fonts via Cloud S3 / CDN"
                />
                <span className="text-sm font-bold text-slate-800">
                  Serve Studio Frontend Fonts via Cloud S3 / CDN URL
                </span>
              </div>
              <span className={`text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0 ${
                settings.enable_font_offload ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-600'
              }`}>
                {settings.enable_font_offload ? 'Active' : 'Disabled'}
              </span>
            </div>

            <div className="p-5 bg-gradient-to-r from-indigo-50 to-slate-50 rounded-2xl border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <h4 className="text-sm font-bold text-indigo-950">
                  Purge & Resync Remote Fonts from Local Directory
                </h4>
                <p className="text-xs text-indigo-800 mt-1">
                  Source: <code>wp-content/uploads/nbdesigner/fonts/</code>.
                  <br />
                  Uploads all local fonts to S3 with immutable cache and <code>Access-Control-Allow-Origin: *</code>.
                </p>
              </div>
              <button
                type="button"
                onClick={handlePurgeResyncFonts}
                disabled={syncingFonts}
                className="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-sm transition whitespace-nowrap text-xs sm:text-sm cursor-pointer self-start sm:self-center"
              >
                {syncingFonts ? <RefreshCw className="w-4 h-4 animate-spin" /> : <UploadCloud className="w-4 h-4" />}
                {syncingFonts ? 'Syncing Fonts...' : 'Purge & Resync Fonts from Local'}
              </button>
            </div>

            <div className="pt-6 border-t border-slate-100 flex justify-end">
              <button
                type="button"
                onClick={handleSave}
                disabled={saving}
                className="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-md shadow-indigo-500/20 transition cursor-pointer text-sm"
              >
                {saving ? 'Saving...' : 'Save Settings'}
              </button>
            </div>
          </div>
        )}

        {/* TAB 5: STATS & BATCH ACTIONS */}
        {activeTab === 'stats' && (
          <div className="space-y-6">
            <div className="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
              <div className="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                  <h2 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <BarChart3 className="w-5 h-5 text-indigo-600" /> Storage Asset Distribution
                  </h2>
                  <p className="text-sm text-slate-500 mt-1">
                    Live scan of current local folders vs offloaded cloud assets.
                  </p>
                </div>
                <button
                  onClick={fetchStats}
                  disabled={loading}
                  className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer"
                >
                  <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} /> Refresh
                </button>
              </div>

              {stats ? (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                  {/* Card 1 */}
                  <div className="p-5 bg-gradient-to-br from-emerald-50 to-slate-50 border border-emerald-200 rounded-2xl">
                    <span className="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                      <ShieldCheck className="w-4 h-4 text-emerald-600" /> Whitelisted Templates
                    </span>
                    <div className="mt-3 text-3xl font-extrabold text-emerald-950">
                      {stats.templates_count || stats.templates_whitelisted || 0}
                    </div>
                    <p className="text-xs text-emerald-700 mt-1">Protected on local NVMe SSD</p>
                  </div>

                  {/* Card 2 */}
                  <div className="p-5 bg-gradient-to-br from-blue-50 to-slate-50 border border-blue-200 rounded-2xl">
                    <span className="text-xs font-bold uppercase tracking-wider text-blue-700 flex items-center gap-1.5">
                      <HardDrive className="w-4 h-4 text-blue-600" /> Active Local Designs
                    </span>
                    <div className="mt-3 text-3xl font-extrabold text-blue-950">
                      {stats.local_designs_count || 0}
                    </div>
                    <p className="text-xs text-blue-700 mt-1">Folders in <code>designs/</code></p>
                  </div>

                  {/* Card 3 */}
                  <div className="p-5 bg-gradient-to-br from-purple-50 to-slate-50 border border-purple-200 rounded-2xl">
                    <span className="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-1.5">
                      <Cloud className="w-4 h-4 text-purple-600" /> Offloaded in S3
                    </span>
                    <div className="mt-3 text-3xl font-extrabold text-purple-950">
                      {stats.offloaded_orders_count || stats.offloaded_count || 0}
                    </div>
                    <p className="text-xs text-purple-700 mt-1">Hydrated on-demand if opened</p>
                  </div>

                  {/* Card 4 */}
                  <div className="p-5 bg-gradient-to-br from-amber-50 to-slate-50 border border-amber-200 rounded-2xl">
                    <span className="text-xs font-bold uppercase tracking-wider text-amber-700 flex items-center gap-1.5">
                      <Type className="w-4 h-4 text-amber-600" /> Dual-Synced Fonts
                    </span>
                    <div className="mt-3 text-3xl font-extrabold text-amber-950">
                      {stats.local_fonts_count || stats.fonts_count || 0}
                      <span className="text-sm font-normal text-amber-800 ml-2">
                        ({stats.local_fonts_size || '0 MB'})
                      </span>
                    </div>
                    <p className="text-xs text-amber-700 mt-1">Local SSD + S3 CDN edge</p>
                  </div>
                </div>
              ) : (
                <div className="py-10 text-center text-slate-400">Loading storage statistics...</div>
              )}

              {/* Manual Sweep Action */}
              <div className="p-5 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h4 className="text-sm font-bold text-slate-900">Run Full Cold Storage Sweep</h4>
                  <p className="text-xs text-slate-500 mt-0.5">
                    Sweeps all completed orders older than {settings.cold_retention_days} days. (Runs automatically daily via Action Scheduler).
                  </p>
                </div>
                <button
                  type="button"
                  onClick={handleSweepNow}
                  disabled={sweeping}
                  className="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold shadow-sm transition whitespace-nowrap text-xs sm:text-sm cursor-pointer self-start sm:self-center"
                >
                  {sweeping ? <RefreshCw className="w-4 h-4 animate-spin" /> : <UploadCloud className="w-4 h-4" />}
                  {sweeping ? 'Sweeping...' : 'Run Cold Sweep Now'}
                </button>
              </div>
            </div>
          </div>
        )}

      </div>
    </div>
  );
}
