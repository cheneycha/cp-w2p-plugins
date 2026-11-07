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
  });

  const [stats, setStats] = useState(null);
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
    fetchExplorerFolders('all', false, 0, '');
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
      }
    } catch (err) {
      console.error('Failed to load stats:', err);
    } finally {
      setLoading(false);
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
      <div className="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-xl mb-6 relative overflow-hidden">
        <div className="absolute right-0 top-0 translate-x-10 -translate-y-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
          <div>
            <div className="flex items-center gap-2 mb-2">
              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-200 border border-indigo-400/20">
                <Sparkles className="w-3.5 h-3.5 text-indigo-300" /> Web-to-Print Tiered Storage Engine
              </span>
              <span className="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-400/20">
                PHP 8.3 & HPOS Ready
              </span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
              <Cloud className="w-8 h-8 text-indigo-400" /> NBDesigner S3 Offload
            </h1>
            <p className="text-indigo-200 text-sm mt-1 max-w-2xl leading-relaxed">
              Tiered storage lifecycle: Whitelist protected admin templates on local NVMe SSD, offload aged guest designs to S3, and dual-sync fonts with CDN acceleration.
            </p>
          </div>

          <div className="flex items-center gap-3 self-start md:self-center">
            <button
              onClick={handleSave}
              disabled={saving}
              className="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/30 transition disabled:opacity-50 cursor-pointer text-sm"
            >
              {saving ? <RefreshCw className="w-4 h-4 animate-spin" /> : <Check className="w-4 h-4" />}
              {saving ? 'Saving...' : 'Save Settings'}
            </button>
          </div>
        </div>

        {/* Global Toast Notice */}
        {notice && (
          <div
            className={`mt-4 p-4 rounded-xl border text-sm flex items-center justify-between shadow-sm animate-fade-in ${
              notice.type === 'success'
                ? 'bg-emerald-950/80 border-emerald-500/50 text-emerald-200'
                : 'bg-rose-950/80 border-rose-500/50 text-rose-200'
            }`}
          >
            <div className="flex items-center gap-2.5">
              {notice.type === 'success' ? (
                <CheckCircle2 className="w-5 h-5 text-emerald-400 flex-shrink-0" />
              ) : (
                <AlertCircle className="w-5 h-5 text-rose-400 flex-shrink-0" />
              )}
              <span className="font-medium">{notice.message}</span>
            </div>
            <button
              onClick={() => setNotice(null)}
              className="text-slate-400 hover:text-white text-xs font-bold px-2 py-1"
            >
              ✕
            </button>
          </div>
        )}

        {/* Nav Tabs Bar */}
        <div className="flex flex-wrap gap-2 mt-8 pt-4 border-t border-indigo-700/40">
          <button
            onClick={() => {
              setActiveTab('explorer');
              fetchExplorerFolders();
            }}
            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer ${
              activeTab === 'explorer'
                ? 'bg-white text-indigo-900 shadow-md'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Folder className="w-4 h-4" /> 🧪 Trial Sandbox & Explorer
          </button>

          <button
            onClick={() => setActiveTab('connection')}
            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer ${
              activeTab === 'connection'
                ? 'bg-white text-indigo-900 shadow-md'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Cloud className="w-4 h-4" /> Cloud S3 & CDN
          </button>

          <button
            onClick={() => setActiveTab('lifecycle')}
            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer ${
              activeTab === 'lifecycle'
                ? 'bg-white text-indigo-900 shadow-md'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Layers className="w-4 h-4" /> Tiered Storage Policy
          </button>

          <button
            onClick={() => setActiveTab('fonts')}
            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer ${
              activeTab === 'fonts'
                ? 'bg-white text-indigo-900 shadow-md'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <Type className="w-4 h-4" /> Font Dual-Sync
          </button>

          <button
            onClick={() => {
              setActiveTab('stats');
              fetchStats();
            }}
            className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer ${
              activeTab === 'stats'
                ? 'bg-white text-indigo-900 shadow-md'
                : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white'
            }`}
          >
            <BarChart3 className="w-4 h-4" /> Storage Stats & Sweeps
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
                                    {(f.is_offloaded || f.is_template) && (
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

              <div className="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <label className="flex items-center gap-3 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={settings.use_path_style}
                    onChange={(e) => setSettings({ ...settings, use_path_style: e.target.checked })}
                    className="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer"
                  />
                  <span className="text-sm font-bold text-slate-800">
                    Use Path-Style URLs (<code>endpoint/bucket/...</code>)
                  </span>
                </label>
                <p className="text-xs text-slate-500 ml-7 mt-0.5">
                  Recommended for Wasabi, Backblaze B2, MinIO, and DigitalOcean Spaces. (Leave unchecked for standard AWS S3 buckets).
                </p>
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

            {/* Cold Order Offload Setting */}
            <div className="space-y-5 pt-2">
              <div className="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <label className="flex items-start gap-3 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={settings.enable_cold_order_offload}
                    onChange={(e) => setSettings({ ...settings, enable_cold_order_offload: e.target.checked })}
                    className="w-4 h-4 mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300"
                  />
                  <div>
                    <span className="text-sm font-bold text-slate-900">
                      Enable Automated Tier 3 Cold Storage Offload for Orders & Guest Designs
                    </span>
                    <p className="text-xs text-slate-500 mt-0.5">
                      Completed order designs older than the retention threshold will be copied to S3, verified, and local copies purged.
                    </p>
                  </div>
                </label>
              </div>

              {settings.enable_cold_order_offload && (
                <div className="ml-4 pl-4 border-l-2 border-indigo-200 space-y-5 bg-slate-50/60 p-5 rounded-xl border border-slate-200">
                  <div>
                    <div className="flex items-center justify-between mb-2">
                      <label className="text-xs font-bold uppercase tracking-wider text-slate-700">
                        Cold Retention Threshold
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

              {/* Draft Garbage Collection */}
              <div className="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <label className="flex items-start gap-3 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={settings.cleanup_abandoned_drafts}
                    onChange={(e) => setSettings({ ...settings, cleanup_abandoned_drafts: e.target.checked })}
                    className="w-4 h-4 mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300"
                  />
                  <div>
                    <span className="text-sm font-bold text-slate-900">
                      Tier 5: Garbage Collect Abandoned Guest Drafts & Temporary Uploads
                    </span>
                    <p className="text-xs text-slate-500 mt-0.5">
                      Cleans up orphan unpurchased session uploads and temporary files older than {settings.abandoned_drafts_days} days to keep SSD disks lean.
                    </p>
                  </div>
                </label>
              </div>
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

            <div className="p-4 bg-slate-50 rounded-xl border border-slate-200">
              <label className="flex items-center gap-3 cursor-pointer">
                <input
                  type="checkbox"
                  checked={settings.enable_font_offload}
                  onChange={(e) => setSettings({ ...settings, enable_font_offload: e.target.checked })}
                  className="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300"
                />
                <span className="text-sm font-bold text-slate-800">
                  Serve Studio Frontend Fonts via Cloud S3 / CDN URL
                </span>
              </label>
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
